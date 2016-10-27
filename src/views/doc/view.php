<?php

use vr\api\components\widgets\ControllersListView;
use vr\api\components\widgets\InputParamsView;
use vr\api\models\ControllerModel;
use yii\helpers\Url;

/** @var ControllerModel $model */
/** @var ControllerModel[] $controllers */

?>

<div class="row">
    <div class="col-sm-2">
        <?= ControllersListView::widget([
            'models' => $controllers,
        ]) ?>
    </div>

    <div class="col-sm-10">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="label label-default"><?= implode(',', $model->verbs) ?></span>
                        <?= $model->route ?>

                        <button id="execute" class="btn btn-default pull-right btn-xs"
                                data-url="<?= Url::to($model->route, true) ?>">Execute
                        </button>
                    </div>
                    <div class="panel-body">
                        <p>
                            <?= $model->description ?>
                        </p>

                        <pre><code class="json editable" contenteditable="true" id="request-block"><?=
                                InputParamsView::widget([
                                    'params' => $model->getInputParams(),
                                ]) ?></code></pre>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="panel panel-default response-block hidden">
                    <div class="panel-heading">
                        Response

                        <button class="btn btn-default pull-right btn-xs" data-clipboard-target="#response-text">
                            Copy
                        </button>
                    </div>
                    <div class="panel-body">
                        <pre><code class="json" id="response-text"></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>