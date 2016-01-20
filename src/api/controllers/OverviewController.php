<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\controllers;

/**
 * Class OverviewController
 * @package vm\api\controllers
 */
class OverviewController extends \yii\web\Controller
{
    /**
     * @var string
     */
    public $layout = '@api/views/layouts/main';

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('@api/views/overview/index');
    }
}