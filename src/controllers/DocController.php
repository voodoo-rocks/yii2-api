<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vr\api\controllers;

use vr\api\components\Harvester;
use Yii;
use yii\base\Module;
use yii\web\Controller;

/**
 * Class DocController
 * @package vr\api\controllers
 */
class DocController extends Controller
{
    /**
     * @var string
     */
    public $layout = '@api/views/layouts/main';

    /**
     * @param null $route
     *
     * @return string
     */
    public function actionIndex($route = null)
    {
        /** @var Harvester $harvester */
        $harvester = Yii::$app->controller->module->get('harvester');

        /** @var Module $module */
        $module = \Yii::$app->controller->module;

        $controllers = $harvester->getControllers($module);

        if ($route) {
            return $this->render('@api/views/doc/view', [
                'controllers' => $controllers,
                'model'       => $harvester->findAction($module, $route),
                'includeMeta' => Yii::$app->session->get('include-meta', false),
            ]);
        }

        return $this->render('@api/views/doc/index', [
            'controllers' => $controllers,
        ]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionToggleMeta()
    {
        Yii::$app->session->set('include-meta', !Yii::$app->session->get('include-meta', false));

        return $this->redirect(Yii::$app->request->referrer ?: 'index');
    }

    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        Yii::$app->request->enableCsrfValidation = false;
        Yii::$app->assetManager->forceCopy       = defined('YII_DEBUG') && YII_DEBUG;

        return parent::beforeAction($action);
    }
}