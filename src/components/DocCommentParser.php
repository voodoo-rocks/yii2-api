<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 23:36
 */

namespace vr\api\components;

use yii\base\Object;
use yii\helpers\ArrayHelper;

/**
 * Class DocCommentParser
 * @package vr\api\components
 */
class DocCommentParser extends Object
{
    /**
     * @var
     */
    public $source;

    /**
     * @var
     */
    public $params;

    /**
     *
     */
    public function init()
    {
    }

    /**
     *
     */
    public function getParams()
    {
    }

    /**
     *
     */
    public function getReturn()
    {
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return trim(ArrayHelper::getValue(explode(PHP_EOL, $this->source), 1), " *");
    }
}