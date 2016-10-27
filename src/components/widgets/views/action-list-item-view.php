<?php

use vr\api\models\ActionModel;
use yii\helpers\Url;

/** @var ActionModel $model */

$active = Yii::$app->request->get('route', null) == $model->route;

?>

<a href="<?= Url::current(['route' => $model->route]) ?>"
   class="list-group-item <?= $active ? 'active' : null ?>">
    <?= $model->label ?>
</a>
