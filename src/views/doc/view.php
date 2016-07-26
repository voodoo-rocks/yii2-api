<?php
use vm\api\components\widgets\RequestNode;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * @var string $controller
 * @var string $action
 */

/** @var string $route */
$route = Url::to(Yii::$app->controller->module->uniqueId . '/' . $controller . '/' . $action);

/** @var Controller $instance */
list($instance) = Yii::$app->createController($route);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="well">
            <div class="pull-left">
                <h4><?= $route ?></h4>
            </div>

            <div class="pull-right">
                <?= Html::button(null, [
                    'autocomplete' => 'off',
                    'class'        => 'btn btn-success btn-api-call',
                    'data'         => [
                        'loading-text' => 'Executing...',
                        'url'          => Url::toRoute('/' . $route, true),
                        'request'      => sha1($route) . '-request',
                        'response'     => sha1($route) . '-response',
                    ],
                ]) ?>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h4>Request</h4>

        <div id="<?= sha1($route); ?>-request" class="json well">
            {
            <ul class="list-json">
                <?php
                if ($instance && $action) {
                    $template = $instance->runAction($action, ['verbose' => true]);

                    echo RequestNode::widget([
                        'node' => $template
                    ]);
                }
                ?>
            </ul>
            }
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <h4>Response</h4>
    </div>

    <div class="col-xs-6 text-right">
        <i class="glyphicon glyphicon-time"></i> <span class="execution-time"> - </span>ms

        <span class="status label"></span>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <pre id="<?= sha1($route); ?>-response" class="json">Please press Call button to see the response</pre>
    </div>
</div>