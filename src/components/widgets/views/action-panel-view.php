<?php

/** @var Action $model */
use vr\api\models\Action;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="label label-default"><?= implode(',', $model->verbs) ?></span>
        <?= $model->route ?>

        <button class="btn btn-default pull-right btn-xs">Execute</button>
    </div>
    <div class="panel-body">
        <?= $model->docParser->description ?>
    </div>
</div>