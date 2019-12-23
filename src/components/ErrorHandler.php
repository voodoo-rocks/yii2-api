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

/**
 * Class ErrorHandler
 * @package vr\api\components
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * @param Error|Exception $exception
     *
     * @return array
     */
    protected function convertExceptionToArray($exception)
    {
        $array = parent::convertExceptionToArray($exception);

        if ($exception instanceof ErrorsException) {
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