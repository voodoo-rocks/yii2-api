<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 25/11/2016
 * Time: 15:21
 */

namespace vr\api\components;

/**
 * Class JsonResponseFormatter
 * @package vr\api\components
 */
class JsonResponseFormatter extends \yii\web\JsonResponseFormatter
{
    /**
     * Formats response data in JSON format.
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
            $response->content = Json::encode($response->data, $options);
        }
    }
}