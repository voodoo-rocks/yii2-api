<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\components;

use yii\helpers\Json;

/**
 * Class JsonResponseFormatter
 * @package vm\api\components
 */
class JsonResponseFormatter extends \yii\web\JsonResponseFormatter
{
    /**
     * @param \yii\web\Response $response
     */
    protected function formatJson($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        if ($response->data !== null) {
            $response->content = Json::encode($response->data, JSON_PRETTY_PRINT);
        }
    }
}