<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api;

use vr\api\components\Controller;
use vr\api\components\ErrorHandler;
use vr\api\components\Response;
use vr\api\doc\components\Harvester;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Request;

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
    /**
     * @var bool
     */
    public $docEnabled = YII_DEBUG;

    /**
     * @var bool
     */
    public $purifyDoc = !YII_DEBUG;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        Yii::setAlias('@api', __DIR__ . DIRECTORY_SEPARATOR);

        $this->set('harvester', new Harvester());

        $this->defaultRoute  = 'doc/index';
        $this->controllerMap = [
            'doc' => Controller::class,
        ];

        $this->setUpUser();

        Yii::$app->set('request', [
            'enableCookieValidation' => false,
            'enableCsrfValidation'   => false,

            'class'   => Request::class,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ]);

        (new ErrorHandler())->register();

        Yii::$app->set('response', [
            'class' => Response::class,
        ]);
    }

    /**
     *
     */
    protected function setUpUser()
    {
        $definitions = Yii::$app->getComponents(true);
        $setUp       = ArrayHelper::getValue($definitions, ['user', 'identityClass']);

        if (!empty($setUp)) {
            $user                  = Yii::$app->user;
            $user->enableSession   = false;
            $user->enableAutoLogin = false;
            $user->loginUrl        = null;
        }
    }
}