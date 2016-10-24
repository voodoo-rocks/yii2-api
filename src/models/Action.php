<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 18:58
 */

namespace vr\api\models;

use yii\base\Model;

/**
 * Class Action
 * @package vr\api\models
 */
class Action extends Model
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
    public $docParser;
}