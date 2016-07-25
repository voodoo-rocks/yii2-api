<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api;

use Yii;
use yii\base\Exception;
use yii\web\Response;

/**
 * Class Module
 * @package vm\api
 */
class Module extends \yii\base\Module
{
    /**
     * @var array
     */
    public $controllerMap = [
        'doc'      => 'vm\api\controllers\DocController',
        'android'  => 'vm\api\controllers\AndroidController',
        'tests'    => 'vm\api\controllers\TestsController',
        'overview' => 'vm\api\controllers\OverviewController',
    ];

    /**
     * @throws Exception
     */
    public function init()
    {
        Yii::$app->user->logout();
        Yii::$app->session->destroy();
        parent::init();

        if (!YII_DEBUG) {
            $this->controllerMap = [];
        }

        Yii::setAlias('@yii2vm', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
        Yii::setAlias('@api', __DIR__ . DIRECTORY_SEPARATOR);

        if (!Yii::$app->has('api')) {
            throw new Exception('API component is missing in the config file. Please add a component that is object of @vm\api\components\Api');
        }

        /** @noinspection PhpUndefinedFieldInspection */
        if (Yii::$app->api->enableDocs) {
            $this->controllerMap['doc'] = 'vm\api\controllers\DocController';
        }

        Yii::$app->response->formatters = array_merge(
            Yii::$app->response->formatters,
            [
                Response::FORMAT_JSON => '\vm\api\components\JsonResponseFormatter',
            ]
        );
    }
}