<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\controllers;

use vm\api\models\SdkPrepareForm;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class AndroidController
 * @package vm\api\controllers
 */
class AndroidController extends Controller
{
    /**
     * @var string
     */
    public $layout = '@api/views/layouts/doc';

    /**
     * @return string
     */
    public function actionSdk()
    {
        /** @var SdkPrepareForm $model */
        $model = new SdkPrepareForm();

        if ($model->load(\Yii::$app->request->post())) {
            $model->generate();
        }

        return $this->render('@api/views/android/preview', [
            'model' => $model,
        ]);
    }

    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!class_exists('\ZipArchive')) {
            throw new BadRequestHttpException('Zip extension is not enabled for this server');
        }

        return parent::beforeAction($action);
    }

}