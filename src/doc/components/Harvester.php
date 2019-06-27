<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 15:04
 */

namespace vr\api\doc\components;

use ReflectionMethod;
use vr\api\components\Controller;
use vr\api\components\filters\TokenAuth;
use vr\api\doc\models\ActionModel;
use vr\api\doc\models\ControllerModel;
use vr\api\doc\models\ModuleModel;
use vr\core\Inflector;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class Harvester
 * @package vr\api\doc\components
 * @property array $modules
 */
class Harvester extends Component
{
    /**
     * @var array
     */
    private $_modules = [];

    /**
     * @param yii\base\Module $root
     * @param string $path
     *
     * @return array
     * @throws \ReflectionException
     */
    public function fetchModules($root = null, $path = null)
    {
        $root = $root ?: Yii::$app;

        foreach ($root->getModules() as $alias => $id) {
            $instance = $root->getModule($alias);

            if (is_subclass_of($instance, \vr\api\Module::class)) {
                $relative = array_merge($path ? explode('/', $path) : [], [$alias]);

                /** @noinspection PhpDeprecationInspection */
                $module              = new ModuleModel([
                    'label' => implode('/', $relative),
                    'path'  => $relative,
                ]);
                $module->controllers = $this->fetchControllers($instance);

                $this->_modules[$alias] = $module;
                $this->_modules         =
                    array_merge($this->_modules, $this->fetchModules($instance, $alias));
            }
        }

        return $this->_modules;
    }

    /**
     * @param yii\base\Module $module
     *
     * @return array
     * @throws \ReflectionException
     */
    public function fetchControllers($module)
    {
        $controllers = [];

        foreach ($module->controllerMap as $route => $class) {
            $controllers[] = new ControllerModel([
                'route' => $module->uniqueId . '/' . $route,
                'label' => Inflector::camel2words($route),
            ]);
        }

        $files = FileHelper::findFiles($module->controllerPath, ['only' => ['*Controller.php']]);

        foreach ($files as $file) {
            $class = pathinfo($file, PATHINFO_FILENAME);
            $route = Inflector::camel2id($class = substr($class, 0, strlen($class) - strlen('Controller')));

            $controllers[] = new ControllerModel([
                'route' => $module->uniqueId . '/' . $route,
                'label' => Inflector::camel2words($class),
            ]);
        }

        ArrayHelper::multisort($controllers, 'label');

        foreach ($controllers as $index => $controller) {
            if (!$this->fetchActions($controller)) {
                unset($controllers[$index]);
                continue;
            };

            if ($controller->isActive) {
                unset($controllers[$index]);
                array_unshift($controllers, $controller);
            }
        }

        return $controllers;
    }

    /**
     * @param ControllerModel $controller
     * @return int
     * @throws \ReflectionException
     */
    private function fetchActions(ControllerModel $controller)
    {
        try {
            /** @var Controller $instance */
            list($instance) = \Yii::$app->createController($controller->route);
        } catch (InvalidConfigException $exception) {
            return 0;
        }

        $reflection = new \ReflectionClass($instance);

        /** @var VerbFilter $filter */
        $filter = ArrayHelper::getValue($instance->behaviors(), 'verbs');

        /** @var ReflectionMethod $method */
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($route = $this->extractActionRoute($method)) {

                $docParser = new DocCommentParser([
                    'source' => $method->getDocComment(),
                ]);

                $action = new ActionModel([
                    'verbs'       => $filter ? ArrayHelper::getValue($filter, ['actions', $route], []) : ['get'],
                    'route'       => $controller->route . '/' . $route,
                    'description' => $docParser->getDescription(),
                    'label'       => Inflector::camel2words($route),
                ]);

                $this->updateActionAuthLevel($instance, $action);

                $controller->actions[] = $action;
            }
        }

        ArrayHelper::multisort($controller->actions, 'label');

        return count($controller->actions);
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return null|string
     */
    private function extractActionRoute(ReflectionMethod $method)
    {
        if (substr($method->getName(), 0, strlen('action')) == 'action'
            && $method->getName() != 'actions'
        ) {
            return Inflector::camel2id(substr($method->getName(), strlen('action')));
        }

        return null;
    }

    /**
     * @param Controller $instance
     * @param ActionModel $action
     */
    private function updateActionAuthLevel($instance, $action)
    {
        $filter = new TokenAuth([
            'except'   => $instance->authExcept,
            'optional' => $instance->authOptional,
            'only'     => $instance->authOnly,
        ]);

        $actionInstance = $instance->createAction($action->id);
        $level          = $filter->getAuthLevel($actionInstance);

        $action->authLevel = $level;
    }

    /**
     * @param yii\base\Module $module
     * @param string $route
     *
     * @return ActionModel|null
     * @throws \ReflectionException
     */
    public function findAction($module, $route)
    {
        /** @var ControllerModel $controller */
        foreach ($this->fetchControllers($module) as $controller) {
            if ($action = $controller->findAction($route)) {
                return $action;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->_modules;
    }

    /**
     * @param \yii\base\Module $module
     * @return mixed
     */
    public function getControllers(\yii\base\Module $module)
    {
        return $this->_modules[$module->uniqueId]->controllers;
    }

}