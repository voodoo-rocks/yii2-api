<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 23/03/2017
 * Time: 16:11
 */

namespace vr\api\components;

use Error;
use Exception;
use InvalidArgumentException;
use vr\core\ErrorsException;
use vr\core\Inflector;
use vr\core\utils\HttpCode;
use yii\web\JsonResponseFormatter;

/**
 * Class Response
 * @package vr\api\components
 */
class Response extends \yii\web\Response
{
    /**
     * @var bool
     */
    public $camelize = false;

    /**
     * @var array
     */
    public $formatters = [
        Response::FORMAT_JSON => [
            'class'         => JsonResponseFormatter::class,
            'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE,
            'prettyPrint'   => true,
        ],
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_BEFORE_SEND, function ($event) {
            /** @var self $response */
            $response = $event->sender;

            if ($response->format == Response::FORMAT_JSON) {
                $this->handleJsonResponse($response);
            }
        });
    }

    /**
     * @param Response $response
     */
    protected function handleJsonResponse(Response $response)
    {
        if (!$response->data) {
            $response->data = [];
        }

        if ($this->camelize) {
            $response->data = $this->camelize($this->data);
        }

        if ($response->isSuccessful) {
            $response->data = ['success' => $response->isSuccessful] + $response->data;
        } else {
            $response->data = [
                'success'   => false,
                'exception' => $response->data,
            ];
        }
    }

    /**
     * @param $array
     * @return mixed
     */
    private function camelize($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->camelize($value);
            }

            unset($array[$key]);
            $key = lcfirst(Inflector::camelize($key));

            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Sets the response status code based on the exception.
     *
     * @param Exception|Error $e the exception object.
     *
     * @return $this the response object itself
     * @throws InvalidArgumentException if the status code is invalid.
     * @since 2.0.12
     */
    public function setStatusCodeByException($e): Response
    {
        if ($e instanceof ErrorsException) {
            return $this->setStatusCode(HttpCode::BAD_REQUEST);
        }

        return parent::setStatusCodeByException($e);
    }
}