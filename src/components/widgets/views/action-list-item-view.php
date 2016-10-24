<?php

use vr\api\models\Action;
use yii\helpers\Url;

/** @var Action $model */

?>

<a href="<?= Url::current(['route' => $model->route]) ?>" class="list-group-item">
    <?= $model->label ?>
</a>
