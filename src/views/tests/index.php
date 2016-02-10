<div class="container">
    <a href="#" class="btn btn-primary pull-right run-tests">Run</a>
    <?php

    use yii\helpers\Html;

    $metadata = new \vm\core\Metadata();
    $modules  = $metadata->getModulesOf(\vm\api\Module::className());

    foreach ($modules as $module => $moduleClass) {
        echo Html::tag('h1', $module, ['class' => 'page-header']);

        $controllers = $metadata->getModuleControllers(new $moduleClass($module));
        foreach ($controllers as $controller => $actions) {
            echo $this->render('partials/controller', [
                'module'     => $module,
                'controller' => $controller,
                'actions'    => $actions
            ]);
        }
    }

    ?>

</div>