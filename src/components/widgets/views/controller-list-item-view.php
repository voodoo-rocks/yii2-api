<?php
use vr\api\models\ControllerModel;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

/** @var ControllerModel $model */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php if ($model->isActive) : ?>
            <b><?= $model->label ?></b>
        <?php else: ?>
            <?= $model->label ?>
        <?php endif; ?>
    </div>

    <?= ListView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model->getActions(),
        ]),
        'options' => [
            'class' => 'list-group',
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

<?php if ($model->isActive) : ?>
    <hr>
<?php endif; ?>
