<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/07/2018
 * Time: 22:26
 */

namespace vr\api\src\components;

use yii\web\Controller;

/**
 * Class CorsController
 * @package vr\api\src\components
 */
class CorsController extends Controller
{
    /**
     * @return array
     */
    public function actions()
    {
        return [
            'options' => [
                'class'             => \vr\api\src\components\OptionsAction::class,
            ],
        ];
    }
}