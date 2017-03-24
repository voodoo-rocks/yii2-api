<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 23/03/2017
 * Time: 16:11
 */

namespace vr\api\components;


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
            $response = $event->sender;

            if ($response->format == Response::FORMAT_JSON) {
                if (!$response->data) {
                    $response->data = [];
                }

                if ($response->isSuccessful) {
                    $response->data = ['success' => $response->isSuccessful] + $response->data;
                } else {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'exception' => $response->data,
                    ];
                }
            }
        });
    }
}