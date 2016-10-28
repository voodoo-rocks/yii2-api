<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 18:58
 */

namespace vr\api\models;

use vr\api\components\filters\TokenAuth;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Action
 * @package vr\api\models
 * @property bool $requiresAuthentication
 */
class ActionModel extends Model
{
    /**
     * @var
     */
    public $route;

    /**
     * @var
     */
    public $label;

    /**
     * @var string[]
     */
    public $verbs;

    /**
     * @var string
     */
    public $description;

    /**
     * @var ControllerModel
     */
    public $controllerModel = null;

    /**
     * @return array
     */
    public function getInputParams()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $params = $this->controllerModel->createInstance()->getActionParams($this->getId(), ['verbose']);

        if (!$params) {
            $params = [];
        }

        if ($this->getRequiresAuthentication()) {
            $token = \Yii::$app->session->get(TokenAuth::DEFAULT_TOKEN_PATH,
                ArrayHelper::getValue($params, TokenAuth::DEFAULT_TOKEN_PATH));

            $params = [TokenAuth::DEFAULT_TOKEN_PATH => $token] + $params;
        }

        return $params;
    }

    /**
     * @return mixed
     */
    private function getId()
    {
        $parts = explode('/', $this->route);

        return ArrayHelper::getValue($parts, count($parts) - 1);
    }

    /**
     * @return bool
     */
    public function getRequiresAuthentication()
    {
        $controller = $this->controllerModel->createInstance();

        /** @var TokenAuth $authenticator */
        $authenticator = ArrayHelper::getValue($controller->behaviors, 'authenticator');
        if (!$authenticator) {
            return false;
        }

        $action = $controller->createAction($this->getId());

        return $authenticator->requiresAuthentication($action);
    }
}