<?php

use vr\api\doc\widgets\ControllersListView;
use vr\api\doc\models\ControllerModel;

/** @var ControllerModel[] $controllers */
?>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group">
            <input class="methods-filter form-control" placeholder="Filter actions..." type="text">
        </div>
        <?= ControllersListView::widget([
            'models' => $controllers,
        ]) ?>
    </div>

    <div class="col-sm-10">
        <div class="row">
            <div class="col-sm-12">
                <?= \vr\api\doc\widgets\Alert::widget() ?>
                Please choose an action on the left side
            </div>
        </div>
    </div>
</div>