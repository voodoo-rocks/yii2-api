<?php

use vr\api\models\ActionModel;
use yii\helpers\Url;

/** @var ActionModel $model */

?>

<a href="<?= Url::current(['route' => $model->route]) ?>" class="list-group-item">
    <?= $model->label ?>
</a>
