<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 23:36
 */

namespace vr\api\doc\components;

use yii\base\BaseObject;

/**
 * Class DocCommentParser
 * @package vr\api\doc\components
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
        $string = explode(PHP_EOL, $this->source);
        $values = array_splice($string, 1);

        array_walk($values, function (&$item) {
            $item = trim($item, "* /\t\r\n");
        });

        $filtered = array_filter($values, function ($string) {
            return !empty($string)
                && strpos($string, '@throws') === false
                && strpos($string, '@return') === false;
        });

        return implode('<br/>', $filtered);
    }
}