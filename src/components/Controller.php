<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vr\api\components;

use Yii;
use yii\filters\auth\QueryParamAuth;
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
     * @return array
     */
    public function behaviors()
    {
        $filters = [
            'apiChecker'    => [
                'class' => ApiCheckerFilter::className(),
            ],
            'authenticator' => [
                'class' => QueryParamAuth::className()
            ],

        ];

        return array_merge(parent::behaviors(), $filters);
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed            $result
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
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @noinspection PhpUndefinedFieldInspection */
        if (Yii::$app->has('api') && Yii::$app->api->enableProfiling) {
            Yii::beginProfile($action->uniqueId);
        }

        if (!parent::beforeAction($action) || !$this->checkContentType()) {
            return false;
        };

        return true;
    }

    private function checkContentType()
    {
        $found = ArrayHelper::getValue(Yii::$app->get('request'),
            ['parsers', Yii::$app->request->getContentType()]);

        if (!$found) {
            $acceptable = ArrayHelper::getValue(Yii::$app->get('request'), 'parsers', []);

            throw new BadRequestHttpException('Incorrect content type. Following content types are acceptable: ' .
                                              implode(',', array_keys($acceptable)));
        }

        return true;
    }
}