<?php
use vm\api\components\ModuleAssets;
use vm\api\Module;
use vm\core\Metadata;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

ModuleAssets::register($this) ?>
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

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/highlight.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<?php $this->beginBody() ?>

<?php

$modules = (new Metadata())->getModulesOf(Module::className());

$items = ArrayHelper::getColumn(array_keys($modules), function ($module) {
    return [
        'label' => $module,
        'url'   => Url::to('@web/' . $module . '/doc/index'),
    ];
});

NavBar::begin([
    'brandLabel'            => Yii::$app->name . ' ' . Yii::$app->api->version,
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
    'items'   => [
        ['label' => 'Overview', 'url' => ['overview/index']],
        [
            'label' => 'Module: ' . Yii::$app->controller->module->uniqueId,
            'url'   => '#',
            'items' => $items,
        ],
        ['label' => 'Tests', 'url' => ['tests/index']],
    ],
]);

NavBar::end();
?>

<!-- Begin page content -->
<div class="container-fluid" style="padding-top: 60px">
    <?= $content; ?>
</div>

<footer class="footer">
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage(); ?>
