<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\controllers;

use Yii;
use yii\helpers\Inflector;
use yii\web\Controller;

/**
 * Class DocController
 * @package vm\api\controllers
 */
class DocController extends Controller
{
    /**
     * @var string
     */
    public $layout = '@api/views/layouts/doc';

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('@api/views/doc/index');
    }

    /**
     * @param      $controller
     * @param null $action
     *
     * @return string
     */
    public function actionView($controller, $action = null)
    {
        if ($action) {
            $this->view->title = sprintf('%s > %s - %s',
                Inflector::camel2words($controller),
                Inflector::camel2words($action),
                Yii::$app->name);

            return $this->render('@api/views/doc/view', [
                'controller' => $controller,
                'action'     => $action,
            ]);
        } else {
            return $this->render('@api/views/doc/overview', [
                'controller' => $controller,
            ]);
        }
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

    /**
     *
     */
    public function actionEditable()
    {
    }
}