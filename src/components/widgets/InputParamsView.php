<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/10/2016
 * Time: 20:34
 */

namespace vr\api\components\widgets;

use vr\api\models\MetaModel;
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
     * @var bool
     */
    public $includeMeta = false;

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
        if (!$this->params) {
            $this->params = [];
        }

        $extra = [];

        if ($this->includeMeta) {
            $extra += ['meta' => new MetaModel()];
        }

        return Json::encode($extra + $this->params, JSON_PRETTY_PRINT);
    }
}