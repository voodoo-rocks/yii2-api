<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 14/04/2019
 * Time: 12:48
 */

namespace vr\api\components;

use vr\core\ErrorsException;

/**
 * Class ErrorHandler
 * @package vr\api\components
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * @param \Error|\Exception $exception
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
        }

        return $array;
    }
}