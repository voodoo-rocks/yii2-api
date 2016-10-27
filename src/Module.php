<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vr\api;

use vr\api\components\ErrorHandler;
use vr\api\components\Harvester;
use Yii;
use yii\base\Exception;
use yii\web\Request;
use yii\web\Response;

/**
 * Class Module
 * @package vr\api
 */
class Module extends \yii\base\Module
{
    /**
     * @var array
     */
    public $controllerMap = [
        'doc'   => 'vr\api\controllers\DocController',
        'tests' => 'vr\api\controllers\TestsController',
    ];

    public $hiddenMode = false;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if (\Yii::$app->user) {
            \Yii::$app->user->enableSession = false;
            \Yii::$app->user->loginUrl      = null;
        }

        if (!YII_DEBUG) {
            $this->controllerMap = [];
        }

        Yii::setAlias('@api', __DIR__ . DIRECTORY_SEPARATOR);
        Yii::setAlias('@vendor', __DIR__ . '/../vendor');

        /** @noinspection PhpUndefinedFieldInspection */
        if (YII_DEBUG || (Yii::$app->has('api') && Yii::$app->api->enableDocs)) {
            $this->controllerMap['doc'] = 'vr\api\controllers\DocController';
        }

        $this->set('harvester', new Harvester());

        Yii::$app->set('request', [
            'enableCookieValidation' => false,
            'enableCsrfValidation'   => false,

            'class'   => Request::className(),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ]);

        Yii::$app->set('response', [
            'class'         => '\yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;

                if ($response->format == Response::FORMAT_JSON) {

                    if ($response->isSuccessful) {
                        $response->data = [
                            'success' => $response->isSuccessful,
                            'data'    => $response->data,
                        ];
                    } else {
                        $response->data = [
                            'success'   => $response->isSuccessful,
                            'data'      => null,
                            'exception' => $response->data,
                        ];
                    }
                }
            },
            'formatters'    => [
                Response::FORMAT_JSON => [
                    'class'         => 'yii\web\JsonResponseFormatter',
                    'prettyPrint'   => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ]);
    }
}