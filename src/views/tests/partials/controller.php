<?php

/** @var string $module */
/** @var string $controller */
?>

<div class="list-group">
    <a href="#" class="list-group-item disabled"><?= $controller ?></a>

    <?php

    use yii\helpers\Url;
    use yii\web\Controller;

    $route = substr(Url::to(['/' . $module . '/' . $controller]), strlen(Url::base()) + 1);

    /** @var Controller $controllerInstance */
    list($controllerInstance) = Yii::$app->createController($route);

    /** @noinspection PhpUndefinedVariableInspection */
    foreach ($actions as $action) {
        $actionInstance = $controllerInstance->createAction($action);
        echo $this->render('action', [
            'actionInstance' => $actionInstance
        ]);
    }

    ?>
</div>