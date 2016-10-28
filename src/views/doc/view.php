<?php

use vr\api\components\widgets\ControllersListView;
use vr\api\components\widgets\InputParamsView;
use vr\api\models\ActionModel;
use vr\api\models\ControllerModel;
use yii\helpers\Url;

/** @var ActionModel $model */
/** @var ControllerModel[] $controllers */
/** @var bool $includeHeader */

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
                        <?php if ($model->requiresAuthentication): ?>
                            <span class="glyphicon glyphicon-lock"></span>
                        <?php endif ?>

                        <?= $model->route ?>

                        <div class="btn-group pull-right" role="group" aria-label="...">
                            <button class="btn btn-default btn-xs" data-clipboard-target="#request-text">
                                Copy
                            </button>

                            <button id="execute" class="btn btn-success btn-xs"
                                    data-url="<?= Url::to(['/' . $model->route], true) ?>">Execute
                            </button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <p>
                            <?= $model->description ?>
                        </p>

                        <pre><code class="json editable" contenteditable="true" id="request-text"><?=
                                InputParamsView::widget([
                                    'params'        => $model->getInputParams(),
                                    'includeHeader' => $includeHeader,
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