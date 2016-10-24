<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 15:04
 */

namespace vr\api\components;

use vr\api\models\Controller;
use Yii;
use yii\base\Component;
use yii\base\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;

/**
 * Class Harvester
 * @package vr\api\components
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
            /** @var \vr\api\Module $module */
            $module = $root->getModule($alias);

            if (is_subclass_of($module, \vr\api\Module::className()) && !$module->hiddenMode) {

                $relative                         = array_merge($path ? explode('/', $path) : [], [$alias]);
                $modules[implode('/', $relative)] = $module->className();

                $modules = array_merge($modules, $this->getModules($module, $alias));
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
        /** @var Controller $controller */
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

        return ArrayHelper::getColumn($files, function ($file) use ($module) {
            $class = pathinfo($file, PATHINFO_FILENAME);
            $route = Inflector::camel2id($class = substr($class, 0, strlen($class) - strlen('Controller')));

            return new Controller([
                'route' => $module->uniqueId . '/' . $route,
                'label' => Inflector::camel2words($class),
            ]);
        });
    }
}