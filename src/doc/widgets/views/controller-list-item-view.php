<?php

use vr\api\doc\models\ControllerModel;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

/** @var ControllerModel $model */
?>

<div class="panel panel-default card mb-3">
    <div class="panel-heading card-header">
        <?= $model->label ?>
    </div>
    <?= ListView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model->actions,
        ]),
        'options'      => [
            'class' => 'list-group list-group-flush',
            'tag'   => 'div',
        ],
        'itemOptions'  => [
            'tag'   => null,
            'class' => 'list-group-item',
        ],
        'itemView'     => 'action-list-item-view',
        'layout'       => '{items}',
    ]) ?>
</div>