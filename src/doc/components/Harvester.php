<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 15:04
 */

namespace vr\api\doc\components;

use vr\api\doc\models\ControllerModel;
use vr\core\Inflector;
use Yii;
use yii\base\Component;
use yii\base\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class Harvester
 * @package vr\api\doc\components
 */
class Harvester extends Component
{
    /**
     * @param Module $root
     * @param string $path
     *
     * @return array
     */
    public function getModules($root = null, $path = null)
    {
        if (!$root) {
            $root = Yii::$app;
        }

        $modules = [];

        foreach ($root->getModules() as $alias => $module) {

            /** @var Module $instance */
            $instance = $root->getModule($alias);

            if (is_subclass_of($instance, \vr\api\doc\Module::class) && !$instance->hiddenMode) {

                $relative = array_merge($path ? explode('/', $path) : [], [$alias]);

                $modules[implode('/', $relative)] = $instance->className();
                $modules                          = array_merge($modules, $this->getModules($instance, $alias));
            }
        }

        return $modules;
    }

    /**
     * @param Module $module
     * @param string $route
     *
     * @return null
     */
    public function findAction($module, $route)
    {
        /** @var ControllerModel $controller */
        foreach ($this->getControllers($module) as $controller) {
            if ($action = $controller->findAction($route)) {
                return $action;
            }
        }

        return null;
    }

    /**
     * @param Module $module
     *
     * @return array
     */
    public function getControllers($module)
    {
        $files = FileHelper::findFiles($module->controllerPath, ['only' => ['*Controller.php']]);

        $controllers      = [];
        $activeController = null;

        foreach ($files as $file) {
            $class = pathinfo($file, PATHINFO_FILENAME);
            $route = Inflector::camel2id($class = substr($class, 0, strlen($class) - strlen('Controller')));

            $controller = new ControllerModel([
                'route' => $module->uniqueId . '/' . $route,
                'label' => Inflector::camel2words($class),
            ]);

            if (!$controller->loadActions()) {
                continue;
            }

            if ($controller->isActive) {
                $activeController = $controller;
            } else {
                $controllers = array_merge($controllers, [$controller]);
            }
        }

        ArrayHelper::multisort($controllers, 'label');

        if ($activeController) {
            $controllers = array_merge([$activeController], $controllers);
        }

        return $controllers;
    }
}