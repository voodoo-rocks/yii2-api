<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 18:58
 */

namespace vr\api\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Action
 * @package vr\api\models
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
     * @var string
     */
    public $description;

    /**
     * @var ControllerModel
     */
    public $controllerModel = null;

    /**
     * @return mixed
     */
    public function getInputParams()
    {
        return $this->controllerModel->createInstance()->getActionParams($this->getId(), ['verbose']);
    }

    /**
     * @return mixed
     */
    private function getId()
    {
        $parts = explode('/', $this->route);

        return ArrayHelper::getValue($parts, count($parts) - 1);
    }
}