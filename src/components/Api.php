<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class Api
 * @package vr\api\components
 * @property string $randomKey
 */
class Api extends Component
{
    /**
     * @var string
     */
    public $version = '1.0';

    /**
     * @var bool
     */
    public $enableDocs = true;

    /**
     * @var bool
     */
    public $enableProfiling = false;

    /**
     * @var bool
     */
    public $requiresKey = false;

    /**
     * @var array
     */
    public $keys = [];

    /**
     * @return mixed
     */
    public function getRandomKey()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return ArrayHelper::getValue(\Yii::$app->api->keys, array_rand(\Yii::$app->api->keys));
    }
}