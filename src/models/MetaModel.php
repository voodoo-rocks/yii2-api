<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 28/10/2016
 * Time: 09:54
 */

namespace vr\api\models;

use DateTimeZone;
use yii\base\Model;

/**
 * Class MetaModel
 * @package vr\api\components\models
 */
class MetaModel extends Model
{
    /**
     * @var
     */
    public $timezone;

    /**
     * @var
     */
    public $version;

    /**
     * @var
     */
    public $bundle;

    /**
     * @var
     */
    public $method;

    /**
     * @var
     */
    /** @noinspection SpellCheckingInspection */
    public $udid;

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->timezone = (new DateTimeZone('Europe/Kaliningrad'))->getOffset(new \DateTime()) / 60;
        $this->version  = \Yii::$app->version;
        $this->bundle   = 'com.example.app';
    }
}