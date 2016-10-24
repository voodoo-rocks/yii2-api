<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vr\api\controllers;

use vr\core\ArrayObject;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class TestsController
 * @package vr\api\controllers
 */
class TestsController extends Controller
{
    /**
     * @var string
     */
    public $layout = '@api/views/layouts/main';

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('@api/views/tests/index');
    }

    /**
     * @param $controller
     * @param $action
     *
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidRouteException
     */
    public function actionRun($controller, $action)
    {
        if (!\Yii::$app->request->isGet) {
            throw new BadRequestHttpException();
        }

        /** @var Controller $instance */
        list($instance) = \Yii::$app->createController($controller);
        if (!$instance) {
            throw new BadRequestHttpException;
        }

        $route = Url::to(['/' . $controller . '/' . $action], true);

        $template = $instance->runAction($action, ['verbose' => true]);
        $template = (new ArrayObject($template))->toArray();

        $output = $this->call($route, $template);
        $result = Json::decode($output);

        if (!$result) {
            throw new BadRequestHttpException($output);
        }

        $succeeded = ArrayHelper::getValue($result, ['response', 'result', 'succeeded']);

        if (!$succeeded) {
            throw new BadRequestHttpException($output);
        }
    }

    /**
     * @param $route
     * @param $template
     *
     * @return mixed
     */
    private function call($route, $template)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $route);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, Json::encode([
            'request' => $template,
        ]));

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        Yii::$app->request->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }
}