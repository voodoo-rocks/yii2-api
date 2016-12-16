<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 28/10/2016
 * Time: 12:33
 */

namespace vr\api\components\filters;

use yii\base\Action;
use yii\filters\auth\AuthMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\UnauthorizedHttpException;

/**
 * Class TokenAuth
 * @package vm\api\components\auth
 */
class TokenAuth extends AuthMethod
{
    const DEFAULT_TOKEN_PATH = 'accessToken';
    /**
     * @var array
     */
    public $accessTokenPath = self::DEFAULT_TOKEN_PATH;

    /**
     * Authenticates the current user.
     *
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     *
     * @return \yii\web\IdentityInterface the authenticated user identity. If authentication information is not
     *                                    provided, null will be returned.
     * @throws \yii\web\UnauthorizedHttpException if authentication information is provided but is invalid.
     */
    public function authenticate($user, $request, $response)
    {
        /** @var array $request */
        /** @noinspection PhpUndefinedFieldInspection */
        $request = Json::decode($request->rawBody);

        $token = ArrayHelper::getValue($request, $this->accessTokenPath);
        if (!$token || !($identity = $user->loginByAccessToken($token))) {
            \Yii::$app->session->remove($this->accessTokenPath);
            throw new UnauthorizedHttpException('Incorrect or expired token provided');
        }

        \Yii::$app->session->set($this->accessTokenPath, $token);

        return $identity;
    }

    /**
     * @param Action $action
     *
     * @return bool
     */
    public function requiresAuthentication($action)
    {
        return $this->isActive($action);
    }
}