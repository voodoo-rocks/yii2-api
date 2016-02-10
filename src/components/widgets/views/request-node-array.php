<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use vm\api\components\widgets\RequestNode;

/**
 * @var $key
 * @var $array
 */
?>

<div class="toggle-properties open">
    <?= $key ?> [
</div>

<ul class="list-json">
    <?= RequestNode::widget(['node' => $array]); ?>
</ul>
]