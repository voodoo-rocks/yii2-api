<?php
/**
 * @var string $content
 */

use vm\core\Metadata;

$this->beginContent('@api/views/layouts/main.php');
?>
<div class="row">
    <div class="col-sm-3">
        <div id="list-menu">
            <?php
            $metadata    = new Metadata();
            $controllers = $metadata->getModuleControllers(Yii::$app->controller->module);

            foreach ($controllers as $controller => $actions) {
                echo $this->render('partials/controller', [
                    'controller' => $controller,
                    'actions'    => $actions,
                ]);
            }
            ?>
        </div>
    </div>

    <div class="col-sm-9">
        <?= $content; ?>
    </div>
</div>
<?php $this->endContent() ?>
