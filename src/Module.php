<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api;

use vr\api\components\Harvester;
use Yii;
use yii\base\Exception;
use yii\web\Request;
use yii\web\Response;

/**
 * Class Module
 * @package vr\api
 *
 * How to set it up
 *          1. Add the following line to your .htaccess
 *              Header set Access-Control-Allow-Origin "*"
 *          2. Replace * with real URLs to prevent security breaches
 *          3. 
 */
class Module extends \yii\base\Module
{
    /**
     * @var array
     */
    public $controllerMap = [
        'doc' => 'vr\api\controllers\DocController',
    ];

    public $hiddenMode = false;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if ($user = \Yii::$app->user) {
            $user->enableSession   = false;
            $user->enableAutoLogin = false;
            $user->loginUrl        = null;
        }

        if (!YII_DEBUG) {
            $this->controllerMap = [];
        }

        $this->set('harvester', new Harvester());
        $this->defaultRoute = 'doc/index';

        Yii::setAlias('@api', __DIR__ . DIRECTORY_SEPARATOR);

        /** @noinspection PhpUndefinedFieldInspection */
        if (YII_DEBUG || (Yii::$app->has('api') && Yii::$app->api->enableDocs)) {
            $this->controllerMap['doc'] = 'vr\api\controllers\DocController';
        }

        Yii::$app->set('request', [
            'enableCookieValidation' => false,
            'enableCsrfValidation'   => false,

            'class'   => Request::className(),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ]);

        Yii::$app->set('response', [
            'class'      => '\vr\api\components\Response',
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