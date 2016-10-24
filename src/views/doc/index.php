<?php

use vr\api\components\widgets\ActionPanelView;
use vr\api\components\widgets\ControllersListView;
use vr\api\models\Controller;

/** @var Controller $model */
/** @var Controller[] $controllers */

?>

<div class="row">
    <div class="col-sm-2">
        <?= ControllersListView::widget([
            'models' => $controllers,
        ]) ?>
    </div>

    <div class="col-sm-10">
        <?= ActionPanelView::widget([
            'model' => $action
        ]) ?>
    </div>
</div>