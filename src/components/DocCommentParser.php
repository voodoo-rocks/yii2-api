<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 23:36
 */

namespace vr\api\components;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Class DocCommentParser
 * @package vr\api\components
 * @property string $description
 */
class DocCommentParser extends BaseObject
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
     * @return string
     */
    public function getDescription()
    {
        return trim(ArrayHelper::getValue(explode(PHP_EOL, $this->source), 1), " *");
    }
}