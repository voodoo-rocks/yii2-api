<?php

namespace vr\api\doc\models;

use yii\base\Model;

/**
 * Class Controller
 * @package vr\api\doc\models
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
    public $actions = [];

    /**
     * @param $route
     *
     * @return null|ActionModel
     */
    public function findAction($route): ?ActionModel
    {
        /** @var ActionModel $action */
        foreach ($this->actions as $action) {
            if ($action->route == $route) {
                return $action;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        foreach ($this->actions as $action) {
            if ($action->isActive) {
                return true;
            }
        }

        return false;
    }
}