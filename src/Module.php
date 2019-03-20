<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api;

use vr\api\components\Controller;
use vr\api\doc\components\Harvester;
use Yii;
use yii\base\Exception;
use yii\web\Request;
use yii\web\Response;

/**
 * Class Module
 * @package vr\api
 * How to set it up
 *          1. Add the following line to your .htaccess
 *              Header set Access-Control-Allow-Origin "*"
 *          2. Replace * with real URLs to prevent security breaches
 *          3.
 */
class Module extends \yii\base\Module
{
    public $hiddenMode = false;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if (\Yii::$container->has('user')) {
            $user                  = \Yii::$app->user;
            $user->enableSession   = false;
            $user->enableAutoLogin = false;
            $user->loginUrl        = null;
        }

        $this->set('harvester', new Harvester());

        $this->defaultRoute  = 'doc/index';
        $this->controllerMap = [
            'doc' => Controller::class,
        ];

        Yii::setAlias('@api', __DIR__ . DIRECTORY_SEPARATOR);

        Yii::$app->set('request', [
            'enableCookieValidation' => false,
            'enableCsrfValidation'   => false,

            'class'   => Request::class,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ]);

        Yii::$app->set('response', [
            'class'      => '\yii\web\Response',
            'formatters' => [
                Response::FORMAT_JSON => [
                    'class'         => '\vr\api\components\JsonResponseFormatter',
                    'prettyPrint'   => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ]);
    }
}