<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 04/03/2017
 * Time: 21:18
 */

namespace vr\api\validators;


use Yii;
use yii\base\DynamicModel;
use yii\validators\Validator;

/**
 * Class AssociatedArrayValidator
 * @package app\modules\api\validators
 */
class AssociatedArrayValidator extends Validator
{
    /**
     * @var
     */
    public $rules;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        if (!is_array($value)) {
            $this->addError($model, $attribute, $this->message, []);
            return;
        }

        $dynamic = DynamicModel::validateData($value, $this->rules);

        if ($dynamic->hasErrors()) {
            foreach ($dynamic->firstErrors as $attribute => $error) {
                $this->addError($model, 'user', $error);
            }
        }
    }
}