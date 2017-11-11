<?php
use vr\api\models\ControllerModel;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

/** @var ControllerModel $model */
?>

<div class="card mb-3">
    <div class="card-header">
        <?= $model->label ?>
    </div>
    <?= ListView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model->getActions(),
        ]),
        'options' => [
            'class' => 'list-group list-group-flush',
            'tag' => 'div',
        ],
        'itemOptions' => [
            'tag' => null,
            'class' => 'list-group-item',
        ],
        'itemView' => 'action-list-item-view',
        'layout' => '{items}',
    ]) ?>
</div>