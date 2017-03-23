<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vr\api\components;

use vr\api\components\filters\ApiCheckerFilter;
use vr\api\components\filters\TokenAuth;
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
    private $verbose = false;

    /**
     * @return array
     */
    public function behaviors()
    {
        $filters = [
            'apiChecker' => [
                'class' => ApiCheckerFilter::className(),
            ],
            'authenticator' => [
                'class' => TokenAuth::className(),
                'except' => $this->authExcept,
                'only' => $this->authOnly,
                'optional' => $this->authOptional,
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                    'text/html' => Response::FORMAT_HTML,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*' => ['post', 'get'],
                ],
            ],
        ];

        return array_merge(parent::behaviors(), $filters);
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     *
     * @return mixed
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

        return $result;
    }

    /**
     * Makes necessary preparation before the action. In this case it sets up the appropriate response format
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    function beforeAction($action)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if (Yii::$app->has('api', true) && Yii::$app->api->enableProfiling) {
            Yii::beginProfile($action->uniqueId);
        }

        if (!parent::beforeAction($action)
            && !Yii::$app->request->getIsGet()
            && !$this->checkContentType()
        ) {
            return false;
        };


        return true;
    }

    /**
     * @return bool
     * @throws BadRequestHttpException
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
     */
    public function getActionParams($action)
    {
        $action = $this->createAction($action);
        $this->verbose = true;

        try {
            $action->runWithParams([]);
        } catch (VerboseException $exception) {
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
    protected function checkInputParams($callable)
    {
        if ($this->verbose) {
            $params = call_user_func($callable);
            throw new VerboseException($params);
        }

        return true;
    }
}