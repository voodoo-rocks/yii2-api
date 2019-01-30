<?php

use vr\api\doc\components\Harvester;
use vr\api\doc\components\ModuleAssets;
use vr\api\doc\models\ModuleModel;
use yii\bootstrap\Nav;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;

ModuleAssets::register($this);

/** @var string $content */
/** @var View $this */
?>

<?php $this->beginPage(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $this->title ?: Yii::$app->name; ?></title>

    <?php $this->head(); ?>
</head>

<body>
<?php $this->beginBody() ?>

<?php

/** @var View $this */
/** @var Harvester $harvester */
$harvester = Yii::$app->controller->module->get('harvester');

$items = ArrayHelper::getColumn($harvester->modules, function (ModuleModel $module) {
    return [
        'label'  => $module->label,
        'url'    => Url::to('@web/' . $module->label . '/doc/index'),
        'active' => Yii::$app->controller->module->uniqueId == $module->label,
    ];
}); ?>

<nav class="navbar navbar-expand-lg navbar-inverse navbar-dark bg-dark rounded-0">
    <a class="navbar-brand" href="#">
        <?= Yii::$app->name . ' ' . ArrayHelper::getValue(Yii::$app->get('api', false), 'version') ?>
    </a>
    <button class="navbar-toggler hidden" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <?= Nav::widget([
        'options' => [
            'class' => 'navbar-nav',
            'tag'   => 'div',
        ],
        'items'   => $items,
    ]); ?>
</nav>

<!-- Begin page content -->
<div class="container-fluid mt-3">
    <?= $content; ?>
</div>

<footer class="footer">
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage(); ?>
