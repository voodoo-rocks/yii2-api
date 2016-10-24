<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 23:36
 */

namespace vr\api\components;

use yii\base\Object;

class DocParser extends Object
{
    public $source;

    public $params;

    public function init()
    {

    }

    public function getParams()
    {
    }

    public function getReturn()
    {
    }

    public function getDescription()
    {
        return trim(explode(PHP_EOL, $this->source)[1], " *");
    }
}