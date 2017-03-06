<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 25/11/2016
 * Time: 15:25
 */

namespace vr\api\components;


use yii\helpers\ArrayHelper;
use yii\helpers\BaseJson;

/**
 * Class Json
 * @package vr\api\components
 */
class Json extends BaseJson
{
    /**
     * @param mixed $data
     * @param array $expressions
     * @param string $expPrefix
     * @return mixed
     */
    protected static function processData($data, &$expressions, $expPrefix)
    {
        $processed = parent::processData($data, $expressions, $expPrefix);


        return self::reformatKeys($processed);
    }

    /**
     * @param $data
     * @return mixed
     */
    private static function reformatKeys($data)
    {
        foreach ($data as $key => $value) {
            ArrayHelper::remove($data, $key);

            if (is_array($value)) {
                $data[Inflector::variablize($key)] = self::reformatKeys($value);
            } else {
                $data[Inflector::variablize($key)] = $value;
            }
        }

        return $data;
    }


}