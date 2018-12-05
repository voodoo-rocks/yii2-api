<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api\components;

use vr\api\components\filters\ApiCheckerFilter;
use vr\api\components\filters\TokenAuth;
use vr\api\doc\components\Harvester;
use vr\api\doc\controllers\DocController;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\log\Logger;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class Controller
 * @package vr\api\components
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @var array
     */
    public $authExcept = [];
    /**
     * @var null
     */
    public $authOnly = null;

    /**
     * @var array
     */
    public $authOptional = [];

    /**
     * @var bool
     */
    public $isAtomic = true;

    /**
     * @var bool
     */
    private $verbose = false;

    /**
     * @return array
     */
    public function behaviors()
    {
        $filters = [
            'contentNegotiator' => [
                'class'   => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml'  => Response::FORMAT_XML,
                    'text/html'        => Response::FORMAT_HTML,
                ],
            ],
            'verbs'             => [
                'class'   => VerbFilter::class,
                'actions' => [
                    '*' => ['post'],
                ],
            ],
            'authenticator'     => [
                'class'    => TokenAuth::class,
                'except'   => $this->authExcept,
                'only'     => $this->authOnly,
                'optional' => $this->authOptional,
            ],
            'apiChecker'        => [
                'class' => ApiCheckerFilter::class,
            ],
        ];

        return array_merge(parent::behaviors(), $filters);
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed            $result
     *
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        /** @noinspection PhpUndefinedFieldInspection */
        if (Yii::$app->has('api') && Yii::$app->api->enableProfiling) {
            list($count, $time) = Yii::getLogger()->getDbProfiling();

            $message = sprintf('Database queries executed: %d, total time: %f sec', $count, $time);
            Yii::getLogger()->log($message, Logger::LEVEL_PROFILE, 'database');

            Yii::endProfile($action->uniqueId);
        }

        if ($this->isAtomic && ($transaction = Yii::$app->db->getTransaction())) {
            $transaction->commit();
        }

        return $result;
    }

    /**
     * Makes necessary preparation before the action. In this case it sets up the appropriate response format
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    function beforeAction($action)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if (Yii::$app->has('api', true) && Yii::$app->api->enableProfiling) {
            Yii::beginProfile($action->uniqueId);
        }

        if (\Yii::$app->request->method === 'OPTIONS') {
            \Yii::$app->response->headers->add('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');

            return false;
        }

        if (!parent::beforeAction($action)
            && !Yii::$app->request->getIsGet()
            && !$this->checkContentType()
        ) {
            return false;
        };

        if ($this->isAtomic) {
            Yii::$app->db->beginTransaction();
        }

        return true;
    }

    /**
     * @param string $id
     * @param array  $params
     *
     * @return mixed|string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     */
    public function runAction($id, $params = [])
    {
        if (Yii::$app->request->isGet) {
            $route = Yii::$app->requestedRoute;

            /** @var Harvester $harvester */
            $module    = \Yii::$app->controller->module;
            $harvester = Yii::$app->controller->module->get('harvester');
            $action    = $harvester->findAction($module, $route);

            if (!$action) {
                Yii::$app->session->addFlash('danger', "Action <b>{$route}</b> does not exist. You have been redirected to the home page");

                return Yii::$app->controller->redirect(['doc/index']);
            }

            return DocController::renderDocView($route);
        }

        return parent::runAction($id, $params);
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    private function checkContentType()
    {
        $contentType = ArrayHelper::getValue(explode(';', Yii::$app->request->getContentType()), 0);

        $found = ArrayHelper::getValue(Yii::$app->get('request'),
            ['parsers', $contentType]);

        if (!$found) {
            $acceptable = ArrayHelper::getValue(Yii::$app->get('request'), 'parsers', []);

            throw new BadRequestHttpException('Incorrect content type. Following content types are acceptable: ' .
                                              implode(',', array_keys($acceptable)));
        }

        return true;
    }

    /**
     * @param $action
     *
     * @return null|array
     * @throws \yii\base\InvalidConfigException
     */
    public function getActionParams($action)
    {
        $action        = $this->createAction($action);
        $this->verbose = true;

        try {
            $action->runWithParams([]);
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (VerboseException $exception) {
            return $exception->params;
        }

        return null;
    }

    /**
     * @param $callable
     *
     * @return bool
     * @throws VerboseException
     */
    protected function checkInputParams($callable = null)
    {
        if ($this->verbose) {
            $params = [];

            if ($callable) {
                $params = call_user_func($callable);
            }

            throw new VerboseException($params);
        }

        return true;
    }
}