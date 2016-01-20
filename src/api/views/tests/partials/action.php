<a href="#" class="list-group-item testable"
   data-url="<?= \yii\helpers\Url::to(['tests/run'], true) ?>"
   data-controller="<?= $actionInstance->controller->uniqueId; ?>"
   data-action="<?= $actionInstance->id; ?>">

    <?= $actionInstance->uniqueId ?>

    <span class="pull-right">
        <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
        <span class="execution-time">-</span> ms
    </span>
</a>