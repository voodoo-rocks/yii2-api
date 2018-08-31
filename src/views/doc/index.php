<?php

use vr\api\components\widgets\ControllersListView;
use vr\api\models\ControllerModel;

/** @var ControllerModel[] $controllers */
?>

<div class="row">
    <div class="col-sm-2">
        <?= $this->render('partials/actions-filter') ?>
        <?= ControllersListView::widget([
            'models' => $controllers,
        ]) ?>
    </div>

    <div class="col-sm-10">
        <div class="row">
            <div class="col-sm-12">
                Please choose an action on the left side
            </div>
        </div>
    </div>
</div>