<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 03/03/2017
 * Time: 00:19
 */

namespace vr\api\components;


use yii\helpers\ArrayHelper;
use yii\helpers\BaseInflector;

/**
 * Class Inflector
 * @package app\modules\api\components
 */
class Inflector extends BaseInflector
{
    /**
     * @param string|array $input
     * @return array|string
     */
    public static function variablize($input)
    {
        if (is_array($input)) {
            return array_combine(
                ArrayHelper::getColumn(array_keys($input), function ($key) {
                    return Inflector::variablize($key);
                }),
                array_values($input)
            );
        }

        return parent::variablize($input);
    }

    /**
     * @param string|array $input
     * @return array|string
     */
    public static function underscore($input)
    {
        if (is_array($input)) {
            return array_combine(
                ArrayHelper::getColumn(array_keys($input), function ($key) {
                    return Inflector::underscore($key);
                }),
                array_values($input)
            );
        }
        return parent::underscore($input);
    }
}