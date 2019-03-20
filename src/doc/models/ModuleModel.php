<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 30/01/2019
 * Time: 22:15
 */

namespace vr\api\doc\models;

use yii\base\Model;

/**
 * Class ModuleModel
 * @package vr\api\doc\models
 */
class ModuleModel extends Model
{
    /**
     * @var
     */
    public $label;

    /**
     * @var
     */
    public $path;

    /**
     * @var array
     */
    public $controllers;
}