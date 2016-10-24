<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 20:55
 */

namespace vr\api\components\widgets;

use vr\api\models\Action;
use yii\bootstrap\Widget;

class ActionPanelView extends Widget
{
    /** @var Action */
    public $model;

    public function run()
    {
        if (!$this->model) {
            return 'Nothing to show';
        }

        return $this->render('@api/components/widgets/views/action-panel-view', [
            'model' => $this->model,
        ]);
    }

}