<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 15:57
 */

namespace vr\api\widgets;

use yii\bootstrap\Widget;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;

/**
 * Class ControllersListView
 * @package vr\api\widgets
 */
class ControllersListView extends Widget
{
    /**
     * @var null
     */
    public $models = null;

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        return ListView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $this->models,
            ]),
            'layout'       => '{items}',
            'itemView'     => '@api/widgets/views/controller-list-item-view',
        ]);
    }
}