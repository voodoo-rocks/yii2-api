<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 28/10/2016
 * Time: 11:43
 */

namespace vr\api\components;

use vr\api\models\MetaModel;

/**
 * Class Model
 * @package vr\api\components
 */
class Model extends \yii\base\Model
{
    /**
     * @var MetaModel
     */
    public $meta;

    /**
     * @var
     */
    public $self;

    /**
     *
     */
    public function init()
    {
        $this->self = $this;
        parent::init(); // TODO: Change the autogenerated stub
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => NullBehaviour::className(),
            ],
        ];
    }

}