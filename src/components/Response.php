<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 23/03/2017
 * Time: 16:11
 */

namespace vr\api\components;


use vr\api\controllers\DocController;
use vr\api\Module;
use Yii;

class Response extends \yii\web\Response
{
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_BEFORE_SEND, function ($event) {
            $response = $event->sender;

            if ($response->format == Response::FORMAT_HTML) {
                $route = \Yii::$app->requestedRoute;

                /** @var Harvester $harvester */
                $module = \Yii::$app->controller->module;
                $harvester = Yii::$app->controller->module->get('harvester');
                $action = $harvester->findAction($module, $route);

                if ($action) {
                    $response->data = $this->renderDocView($route);
                }
            }

            if ($response->format == Response::FORMAT_JSON) {
                if (!$response->data) {
                    $response->data = [];
                }

                if ($response->isSuccessful) {
                    $response->data = ['success' => $response->isSuccessful] + $response->data;
                } else {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'exception' => $response->data,
                    ];
                }
            }
        });
    }

    private function renderDocView($route)
    {
        /** @var Harvester $harvester */
        $harvester = Yii::$app->controller->module->get('harvester');

        /** @var Module $module */
        $module = \Yii::$app->controller->module;

        $controllers = $harvester->getControllers($module);

        return (new DocController('doc', $module))->render('@api/views/doc/view', [
            'controllers' => $controllers,
            'model' => $harvester->findAction($module, $route),
            'includeMeta' => Yii::$app->session->get('include-meta', false),
        ]);
    }
}