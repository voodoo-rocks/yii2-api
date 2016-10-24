<?php

namespace vr\api\models;

use ReflectionMethod;
use vr\api\components\DocParser;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class Controller
 * @package vr\api\models
 */
class Controller extends \yii\base\Model
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
     * @var \yii\rest\Controller
     */
    private $instance;

    /**
     * @return null
     */
    public function init()
    {
        parent::init();

        return $this->createInstance();
    }

    /**
     * @return null
     */
    public function createInstance()
    {
        /** @var \yii\web\Controller $instance */
        list($this->instance, $success) = \Yii::$app->createController($this->route);

        if (!$success) {
            return null;
        }
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

    public function findAction($route)
    {
        /** @var Action $action */
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
        $reflection = new \ReflectionClass($this->instance);

        $actions = [];

        /** @var VerbFilter $filter */
        $filter = ArrayHelper::getValue($this->instance->behaviors(), 'verbs');

        /** @var ReflectionMethod $method */
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($route = $this->extractRoute($method)) {
                $actions[] = new Action([
                    'verbs'     => $filter ? ArrayHelper::getValue($filter, ['actions', $route], []) : ['get'],
                    'route'     => $this->route . '/' . $route,
                    'docParser' => new DocParser([
                        'source' => $method->getDocComment(),
                    ]),
                    'label'     => Inflector::camel2words($route),
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