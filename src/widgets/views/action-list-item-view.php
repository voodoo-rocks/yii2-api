<?php

use vr\api\models\ActionModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var ActionModel $model */

?>

<a href="<?= Url::to(['/' . $model->route]) ?>"
   class="list-group-item <?= $model->isActive ? 'active' : null ?>">
    <?= implode(' ', ArrayHelper::getColumn($model->verbs, function (string $verb) {
        return Html::tag('span', $verb, ['class' => 'label label-success pull-right']);
    })) ?>
    <?= $model->label ?>
</a>
