<?php

use vr\api\models\ActionModel;
use yii\helpers\Url;

/** @var ActionModel $model */

?>

<a href="<?= Url::to(['/' . $model->route]) ?>"
   class="list-group-item <?= $model->isActive ? 'active' : null ?>">
    <?= $model->label ?>
</a>
