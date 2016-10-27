<?php

namespace vr\api\models;

use ReflectionMethod;
use vr\api\components\Controller;
use vr\api\components\DocParser;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class Controller
 * @package vr\api\models
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
        list($instance, $success) = \Yii::$app->createController($this->route);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $instance;
    }

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
        $instance = $this->createInstance();

        $reflection = new \ReflectionClass($instance);

        $actions = [];

        /** @var VerbFilter $filter */
        $filter = ArrayHelper::getValue($instance->behaviors(), 'verbs');

        /** @var ReflectionMethod $method */
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($route = $this->extractRoute($method)) {

                $docParser = new DocParser([
                    'source' => $method->getDocComment(),
                ]);

                $actions[] = new ActionModel([
                    'controllerModel' => $this,
                    'verbs'           => $filter ? ArrayHelper::getValue($filter, ['actions', $route], []) : ['get'],
                    'route'           => $this->route . '/' . $route,
                    'description'     => $docParser->getDescription(),
                    'label'           => Inflector::camel2words($route),
                ]);
            }
        }

        return $actions;
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
}