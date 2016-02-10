<?php
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * @var string $controller
 * @var array  $actions
 */
?>
<div class="list-group">
    <a href="#menu-<?= Inflector::slug($controller) ?>"
       class="list-group-item disabled"
       data-toggle="collapse"
       data-parent="#list-menu"
       aria-expanded="true">

        <?= Inflector::camel2words($controller) ?>
        <span class="badge"><?= count($actions) ?></span>
    </a>

    <div id="menu-<?= Inflector::slug($controller) ?>" class="collapse in">

        <?php
        $route = substr(Url::to([$controller . '/index']), strlen(Url::base()) + 1);
        /** @var Controller $instance */
        list($instance) = Yii::$app->createController($route);

        /** @var AccessControl $behavior */
        $behavior = $instance->getBehavior('access');

        foreach ($actions as $action) {
            $icon = null;

            if ($behavior) {
                $allow = true;

                /** @var AccessRule $rule */
                foreach ($behavior->rules as $rule) {
                    $allow &= $rule->allows($instance->createAction($action),
                        Yii::$app->user,
                        new \yii\web\Request());

                    if (!$allow) {
                        $icon = 'glyphicon-lock';
                        break;
                    }
                }
            }

            $active =
                $action === Yii::$app->request->getQueryParam('action')
                && $controller === Yii::$app->request->getQueryParam('controller') ? 'active' : null;

            echo Html::a(
                Inflector::camel2words($action) .
                Html::tag('span', '', ['class' => 'pull-right glyphicon ' . $icon]),
                [
                    'view',
                    'controller' => $controller,
                    'action'     => $action,
                ], [
                    'class' => 'list-group-item ' . $active,
                ]
            );
        }
        ?>
    </div>

</div>