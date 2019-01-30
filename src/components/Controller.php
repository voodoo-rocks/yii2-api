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
use yii\base\Exception;
use yii\base\UserException;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
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

    public function actionOptions()
    {
        return null;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $filters = [
            'corsFilter'        => [
                'class' => \yii\filters\Cors::class,
            ],
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
                    '*' => ['post', 'options', 'get'],
                ],
            ],
            'authenticator'     => [
                'class'    => TokenAuth::class,
                'except'   => array_merge($this->authExcept ?: [], ['options']),
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
        if (!parent::beforeAction($action) && Yii::$app->request->isPost && !$this->checkContentType()) {
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
     * @throws BadRequestHttpException
     */
    public function runAction($id, $params = [])
    {
        if (Yii::$app->request->isOptions) {
            return parent::runAction('options');
        }

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

        if (Yii::$app->request->isPost) {
            try {
                $data = parent::runAction($id, $params);

                return array_merge(['success' => true], $data ?: []);
            } /** @noinspection PhpRedundantCatchClauseInspection */
            catch (HttpException $e) {
                Yii::$app->response->statusCode = $e->statusCode;

                return $this->convertExceptionToArray($e);
            } catch (UserException $e) {
                return $this->convertExceptionToArray($e);
            }
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    private function checkContentType()
    {
        $contentType = ArrayHelper::getValue(explode(';', Yii::$app->request->getContentType()), 0);
        $found       = ArrayHelper::getValue(Yii::$app->get('request'),
            ['parsers', $contentType]);

        if (!$found) {
            $acceptable = ArrayHelper::getValue(Yii::$app->get('request'), 'parsers', []);

            throw new BadRequestHttpException(
                'Incorrect content type. Following content types are acceptable: '
                . implode(',', array_keys($acceptable)));
        }

        return true;
    }

    /**
     * @param Exception $e
     *
     * @return array
     */
    protected function convertExceptionToArray($e): array
    {
        return [
            'success'   => false,
            'exception' => $array = [
                'name'    => 'Exception',
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
            ],
        ];
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