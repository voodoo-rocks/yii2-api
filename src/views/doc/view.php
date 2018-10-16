<?php

use vr\api\components\filters\TokenAuth;
use vr\api\components\widgets\ControllersListView;
use vr\api\components\widgets\InputParamsView;
use vr\api\models\ActionModel;
use vr\api\models\ControllerModel;
use yii\helpers\Url;

/** @var ActionModel $model */
/** @var ControllerModel[] $controllers */
/** @var bool $includeMeta */

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
                <h4 class="float-left  pull-left">
                    <?php if ($model->getAuthLevel() > TokenAuth::AUTH_LEVEL_NONE): ?>
                        <span class="glyphicon glyphicon-lock"></span>

                        <?php if ($model->getAuthLevel() == TokenAuth::AUTH_LEVEL_OPTIONAL): ?>
                            (optional)
                        <?php endif ?>
                    <?php endif ?>

                    <?= $model->route ?>
                </h4>

                <div class="btn-group float-right btn-group-sm pull-right" role="group" aria-label="...">
                    <button class="btn btn-default btn-light" data-clipboard-target="#request-text">
                        Copy
                    </button>

                    <button id="execute" class="btn btn-success"
                            data-url="<?= Url::to(['/' . $model->route], true) ?>"
                            data-loading-text="Executing...">
                        Execute
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <p>
                    <?= $model->description ?>
                </p>

                <pre><code class="json editable bg-light" contenteditable="true" id="request-text"><?=
                        InputParamsView::widget([
                            'params'      => $model->getInputParams(),
                            'includeMeta' => $includeMeta,
                        ]) ?></code></pre>
            </div>
        </div>

        <div class="response-wrapper hidden">
            <div class="row mt-3">
                <div class="col-sm-12">
                    <h4 class="float-left pull-left">
                        Response
                        <small>received in <span class="exec-time"></span> sec.</small>
                    </h4>

                    <button class="btn btn-default btn-light float-right btn-xs pull-right" data-clipboard-target="#response-text">
                        Copy
                    </button>
                </div>
            </div>

            <div class="row mt-3 response-block">
                <div class="col-sm-12">
                    <pre><code class="json bg-light" id="response-text"></code></pre>
                </div>
            </div>
        </div>

    </div>
</div>