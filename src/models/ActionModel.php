<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/10/2016
 * Time: 18:58
 */

namespace vr\api\models;

use vr\api\components\Controller;
use vr\api\components\filters\TokenAuth;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

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
        /** @var Controller $instance */
        $instance = $this->controllerModel->createInstance();

        $params = $instance->getActionParams($this->getId());

        if (!$params) {
            $params = [];
        }

        $tokenAttribute = ArrayHelper::getValue($instance->getBehavior('authenticator'), 'accessTokenPath');

        if ($this->getAuthLevel() > TokenAuth::AUTH_LEVEL_NONE) {
            $token = ArrayHelper::getValue($params, $tokenAttribute, \Yii::$app->session->get($tokenAttribute));

            if (!$token) {
                $object = \Yii::createObject(\Yii::$app->user->identityClass);

                /** @var ActiveQuery $query */
                $query = call_user_func([$object, 'find']);

                /** @var IdentityInterface $identity */
                if ($identity = $query->orderBy('rand()')->limit(1)->one()) {
                    $token = $identity->getAuthKey();
                }
            }

            $params = [$tokenAttribute => $token] + $params;
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
    public function getAuthLevel()
    {
        $controller = $this->controllerModel->createInstance();

        /** @var TokenAuth $authenticator */
        $authenticator = ArrayHelper::getValue($controller->behaviors, 'authenticator');
        if (!$authenticator) {
            return false;
        }

        $action = $controller->createAction($this->getId());

        return $authenticator->getAuthLevel($action);
    }
}