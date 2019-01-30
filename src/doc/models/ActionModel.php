<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 18:58
 */

namespace vr\api\doc\models;

use vr\api\components\Controller;
use vr\api\components\filters\TokenAuth;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Action
 * @package vr\api\doc\models
 * @property bool   requiresAuthentication
 * @property bool   $authLevel
 * @property string $id
 * @property array  $inputParams
 * @property bool   isActive
 */
class ActionModel extends Model
{
    /**
     * @var
     */
    public $route;

    /**
     * @var
     */
    public $label;

    /**
     * @var string[]
     */
    public $verbs;

    /**
     * @var
     */
    public $authLevel;

    /**
     * @var string
     */
    public $description;

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getInputParams()
    {
        /** @var Controller $instance */
        $instance = \Yii::$app->controller;
        $params   = $instance->getActionParams($this->id) ?: [];

        if ($this->authLevel > TokenAuth::AUTH_LEVEL_NONE) {
            $params = [
                          'accessToken' => ArrayHelper::getValue($params, 'accessToken'),
                      ] + $params;
        }

        return $params;
    }

    /**
     * @return string
     */
    public function getIsActive()
    {
        return \Yii::$app->requestedRoute == $this->route;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        $parts = explode('/', $this->route);

        return ArrayHelper::getValue($parts, count($parts) - 1);
    }
}