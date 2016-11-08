<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 08/11/2016
 * Time: 22:32
 */

namespace vr\api\components;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\Response;

/**
 * Class JsonResponseFormatter
 * @package vr\api\components
 */
class JsonResponseFormatter extends \yii\web\JsonResponseFormatter
{
    /**
     * @var bool
     */
    public $enableCamel = true;

    /**
     * Formats response data in JSON format.
     *
     * @param Response $response
     */
    protected function formatJson($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        if ($response->data !== null) {
            $options = $this->encodeOptions;
            if ($this->prettyPrint) {
                $options |= JSON_PRETTY_PRINT;
            }

            $data = $response->data;

            if ($this->enableCamel) {
                $data = $this->formatKeys($data);
            }

            $response->content = Json::encode($data, $options);
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function formatKeys($data)
    {
        foreach ($data as $key => $value) {
            ArrayHelper::remove($data, $key);

            if (is_array($value)) {
                $data[Inflector::variablize($key)] = $this->formatKeys($value);
            } else {
                $data[Inflector::variablize($key)] = $value;
            }
        }

        return $data;
    }
}