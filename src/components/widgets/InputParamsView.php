<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/10/2016
 * Time: 20:34
 */

namespace vr\api\components\widgets;

use yii\base\Widget;
use yii\helpers\Json;

/**
 * Class InputParamsView
 * @package vr\api\components\widgets
 */
class InputParamsView extends Widget
{
    /**
     * @var
     */
    public $params;

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->params ? Json::encode($this->params, JSON_PRETTY_PRINT) : '{}';
    }
}