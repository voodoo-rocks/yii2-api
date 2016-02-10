<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use dosamigos\editable\Editable;
use yii\web\JsExpression;

/**
 * @var $key
 * @var $value
 */

echo sprintf('%s %s', $key, Editable::widget([
    'type'          => 'text',
    'name'          => uniqid(),
    'value'         => $value,
    'url'           => 'editable',
    'clientOptions' => [
        'pk'        => uniqid(),
        'emptytext' => 'null',
        'display'   => new JsExpression('function(value, sourceData)  {
            if (isNaN(value)) {
                value = \'"\' + value + \'"\';
            }
            $(this).html(value);
        }'),
    ],
])
);
