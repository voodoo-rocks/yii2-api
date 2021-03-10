<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 30/01/2019
 * Time: 20:20
 */

namespace vr\api\doc\components;

use ReflectionException;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class DocAction
 * @package vr\api\doc\components
 */
class DocAction extends Action
{
    /**
     * @var string
     */
    public $viewPath = '@api/doc/views/doc';

    /**
     * @var string
     */
    public $layout = '@api/doc/views/layouts/main';

    /**
     * @return string|Response
     * @throws InvalidConfigException
     * @throws ReflectionException
     * @throws NotFoundHttpException
     */
    public function run()
    {
        $route = Yii::$app->requestedRoute;

        $module = Yii::$app->controller->module;
        if (!@$module->docEnabled) {
            // TODO: probably move to routing
            throw new NotFoundHttpException();
        }

        /** @var Harvester $harvester */
        $harvester = Yii::$app->controller->module->get('harvester');
        $action    = $harvester->findAction($module, $route);

        $harvester->fetchModules();

        $controllers = $harvester->getControllers($module);

        if (!$action) {
            return $this->render("{$this->viewPath}/index", [
                'controllers' => $controllers,
            ]);
        }

        return $this->render('@api/doc/views/doc/view', [
            'controllers' => $controllers,
            'model'       => $action,
        ]);
    }

    /**
     * @param $view
     * @param $params
     *
     * @return string
     */
    private function render($view, $params)
    {
        $currentView = Yii::$app->getView();
        $title       = ArrayHelper::getValue($params, ['model', 'route']);

        $currentView->title = trim("{$title} | " . Yii::$app->name, ' |');

        $content = $currentView->render($view, $params, $this);
        $layout  = Yii::getAlias($this->layout) . '.php';

        return $currentView->renderFile($layout, ['content' => $content], $this);
    }

    /**
     * @return bool
     */
    protected function beforeRun()
    {
        Yii::$app->request->enableCsrfValidation = false;
        Yii::$app->assetManager->forceCopy       = defined('YII_DEBUG') && YII_DEBUG;

        return true;
    }
}