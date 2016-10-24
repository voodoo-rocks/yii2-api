<?php
use vr\api\models\Controller;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

/** @var Controller $model */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= $model->label ?>
    </div>

    <?= ListView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model->getActions(),
        ]),
        'options'      => [
            'class' => 'list-group',
            'tag'   => 'div',
        ],
        'itemView'     => 'action-list-item-view',
        'layout'       => '{items}',
    ]) ?>
</div>