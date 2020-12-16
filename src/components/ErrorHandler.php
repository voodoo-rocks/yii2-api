<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 14/04/2019
 * Time: 12:48
 */

namespace vr\api\components;

use Error;
use Exception;
use vr\core\ErrorsException;
use Yii;

/**
 * Class ErrorHandler
 * @package vr\api\components
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * @var int
     */
    public $errorStatusCode = 400;

    /**
     * @param Error|Exception $exception
     *
     * @return array
     */
    protected function convertExceptionToArray($exception)
    {
        $array = parent::convertExceptionToArray($exception);

        if ($exception instanceof ErrorsException) {
            Yii::$app->response->statusCode = $this->errorStatusCode;

            $array += [
                'data' => $exception->data,
            ];

            if (YII_DEBUG) {
                $array += [
                    'trace' => explode("\n", $exception->getTraceAsString())
                ];
            }
        }

        return $array;
    }
}