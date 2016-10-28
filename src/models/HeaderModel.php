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
 * Class HeaderModel
 * @package vr\api\components\models
 */
class HeaderModel extends Model
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

    public function init()
    {
        parent::init();

        $this->timezone = (new DateTimeZone('Europe/Kaliningrad'))->getOffset(new \DateTime()) / 60;
        $this->version  = \Yii::$app->version;
        $this->bundle   = 'com.example.app';
    }
}