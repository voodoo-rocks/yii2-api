<?php

namespace vr\api\models;

use ReflectionMethod;
use vr\api\components\Controller;
use vr\api\components\DocParser;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class Controller
 * @package vr\api\models
 * @property bool isActive
 */
class ControllerModel extends Model
{
    /**
     * @var
     */
    public $label;

    /**
     * @var
     */
    public $route;

    /**
     * @var
     */
    public $description;

    /**
     * @var
     */
    private $actions = [];

    /**
     * @return null
     */
    public function init()
    {
        return parent::init();
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
        $this->createInstance();
    }

    /**
     * @return Controller
     */
    public function createInstance()
    {
        /** @var \yii\web\Controller $instance */

        try {
            list($instance) = \Yii::$app->createController($this->route);
        } catch (InvalidConfigException $exception) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $instance;
    }

    /**
     * @param $route
     *
     * @return null|ActionModel
     */
    public function findAction($route)
    {
        /** @var ActionModel $action */
        foreach ($this->getActions() as $action) {
            if ($action->route == $route) {
                return $action;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     *
     */
    public function loadActions()
    {
        $instance = $this->createInstance();

        if (!$instance) {
            return false;
        }

        $reflection = new \ReflectionClass($instance);

        /** @var VerbFilter $filter */
        $filter = ArrayHelper::getValue($instance->behaviors(), 'verbs');

        /** @var ReflectionMethod $method */
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($route = $this->extractRoute($method)) {

                $docParser = new DocParser([
                    'source' => $method->getDocComment(),
                ]);

                $action = new ActionModel([
                    'controllerModel' => $this,
                    'verbs'           => $filter ? ArrayHelper::getValue($filter, ['actions', $route], []) : ['get'],
                    'route'           => $this->route . '/' . $route,
                    'description'     => $docParser->getDescription(),
                    'label'           => Inflector::camel2words($route),
                ]);

                $this->actions[] = $action;
            }
        }

        return true;
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return bool
     */
    public function extractRoute($method)
    {
        if (substr($method->getName(), 0, strlen('action')) == 'action'
            && $method->getName() != 'actions'
        ) {
            return Inflector::camel2id(substr($method->getName(), strlen('action')));
        }

        return null;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        foreach ($this->actions as $action) {
            if ($action->isActive) {
                return true;
            };
        }

        return false;
    }
}