<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 18:58
 */

namespace vr\api\doc\models;

use Exception;
use vr\api\components\Controller;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Action
 * @package vr\api\doc\models
 * @property bool requiresAuthentication
 * @property bool $authLevel
 * @property string $id
 * @property array $inputParams
 * @property bool isActive
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
     * @throws InvalidConfigException
     */
    public function getInputParams(): array
    {
        /** @var Controller $instance */
        $instance = Yii::$app->controller;

        return $instance->getActionParams($this->id) ?: [];
    }

    /**
     * @return string
     */
    public function getIsActive()
    {
        return Yii::$app->requestedRoute == $this->route;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getId()
    {
        $parts = explode('/', $this->route);

        return ArrayHelper::getValue($parts, count($parts) - 1);
    }
}