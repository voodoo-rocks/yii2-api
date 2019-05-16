<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 23/03/2017
 * Time: 16:11
 */

namespace vr\api\components;

use DateTime;
use InvalidArgumentException;
use vr\core\ErrorsException;
use Yii;

/**
 * Class Response
 * @package vr\api\components
 */
class Response extends \yii\web\Response
{
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
     * Sets the response status code based on the exception.
     *
     * @param \Exception|\Error $e the exception object.
     *
     * @return $this the response object itself
     * @throws InvalidArgumentException if the status code is invalid.
     * @since 2.0.12
     */
    public function setStatusCodeByException($e)
    {
        if ($e instanceof ErrorsException) {
            return $this->setStatusCode(400);
        }

        return parent::setStatusCodeByException($e);
    }

    /**
     * @param Response $response
     */
    function handleJsonResponse(Response $response)
    {
        if (!$response->data) {
            $response->data = [];
        }

        if ($response->isSuccessful) {
            $response->data = ['success' => $response->isSuccessful] + $response->data;
        } else {
            $response->data = [
                'success'   => false,
                'exception' => $response->data,
            ];
        }

        /** @var Controller $controller */
        $controller = Yii::$app->controller;
        if ($controller->includeExecInfo) {
            $response->data += [
                '_execution' => [
                    'responseCode'   => $response->statusCode,
                    'responseStatus' => $response->statusText,
                    'timestamp'      => DateTime::createFromFormat('U', (int)$controller->requestedAt)
                        ->format('Y-m-d H:i:s'),
                    'executionTime'  => round(microtime(true) - $controller->requestedAt, 3),
                ],
            ];
        }
    }
}