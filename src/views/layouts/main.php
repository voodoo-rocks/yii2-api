<?php
use vr\api\components\Harvester;
use vr\api\components\ModuleAssets;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
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

$items = ArrayHelper::getColumn(array_keys($harvester->getModules()), function ($module) {
    return [
        'label'  => $module,
        'url'    => Url::to('@web/' . $module . '/doc/index'),
        'active' => Yii::$app->controller->module->uniqueId == $module,
    ];
});

/** @noinspection PhpUndefinedFieldInspection */
NavBar::begin([
    'brandLabel'            => Yii::$app->name . ' ' . ArrayHelper::getValue(Yii::$app->get('api', false), 'version'),
    'brandUrl'              => ['overview/index'],
    'options'               => [
        'class' => 'navbar navbar-inverse navbar-fixed-top',
    ],
    'innerContainerOptions' => [
        'class' => 'container-fluid',
    ],
]);

echo Nav::widget([
    'options' => [
        'class' => 'nav navbar-nav',
    ],
    'items'   => $items,
]);

NavBar::end();
?>

<!-- Begin page content -->
<div class="container-fluid" style="padding-top: 60px">
    <?= $content; ?>
</div>

<footer class="footer">
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage(); ?>
