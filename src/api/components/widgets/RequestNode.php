<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vm\api\components\widgets;

use yii\bootstrap\Widget;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ListView;

class RequestNode extends Widget
{
    public $node;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return ListView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $this->node
            ]),
            'itemView'     => function ($model, $key, $index, $widget) {
                $key = !is_numeric($key) ? Html::tag('span', "\"{$key}\"") . ':' : null;


                $optional = is_array($model) && (array_search('optional', $model) !== false);
                $remover  = $this->generateRemover($optional);

                if ($optional) {
                    array_splice($model, array_search('optional', $model), 1);
                    $model = ArrayHelper::getValue($model, 'value', $model);
                }

                if (ArrayHelper::isIndexed($model)) {
                    return $this->render('request-node-array', [
                        'key'   => $key,
                        'array' => $model
                    ]);
                } else if (ArrayHelper::isAssociative($model)) {
                    return $remover . $this->render('request-node-object', [
                        'key'   => $key,
                        'value' => $model
                    ]);
                } else {
                    return $remover . $this->render('request-node-value', [
                        'key'   => $key,
                        'value' => $model
                    ]);
                }
            },
            'itemOptions'  => ['tag' => 'span'],
            'layout'       => '{items}',
            'emptyText'    => false,
            'separator'    => Html::tag('span', ',') . Html::tag('br')
        ]);
    }

    /**
     * @param $optional
     *
     * @return string
     */
    function generateRemover($optional)
    {
        if (!$optional) {
            return null;
        }

        $remover = Html::a(
            Html::tag('i', null, ['class' => 'glyphicon glyphicon-remove text-danger']) . PHP_EOL,
            '#', ['class' => 'node-remover']);

        return $remover;
    }
}