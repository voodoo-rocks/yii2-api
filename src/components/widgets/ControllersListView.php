<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 15:57
 */

namespace vr\api\components\widgets;

use yii\bootstrap\Widget;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

class ControllersListView extends Widget
{
    public $models = null;

    public function run()
    {
        return ListView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $this->models,
            ]),
            'layout'       => '{items}',
            'itemView'     => '@api/components/widgets/views/controller-list-item-view',
        ]);
    }
}