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
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * Class TokenAuth
 * @package vm\api\components\auth
 */
class TokenAuth extends AuthMethod
{
    /**
     *
     */
    const DEFAULT_TOKEN_PATH = 'accessToken';

    /**
     *
     */
    const DEFAULT_TOKEN_HEADER = 'X-Access-Token';

    /**
     *
     */
    const AUTH_LEVEL_REQUIRED = 1;

    /**
     *
     */
    const AUTH_LEVEL_OPTIONAL = 0;

    /**
     *
     */
    const AUTH_LEVEL_NONE = -1;

    /**
     * @var string
     */
    public $accessTokenPath = self::DEFAULT_TOKEN_PATH;

    /**
     * @var string
     */
    public $accessTokenHeader = self::DEFAULT_TOKEN_HEADER;

    /**
     * Authenticates the current user.
     *
     * @param \yii\web\User     $user
     * @param \yii\web\Request  $request
     * @param \yii\web\Response $response
     *
     * @return \yii\web\IdentityInterface the authenticated user identity. If authentication information is not
     *                                    provided, null will be returned.
     * @throws UnauthorizedHttpException if authentication information is provided but is invalid.
     */
    public function authenticate($user, $request, $response)
    {
        $identity = null;
        $token    = ArrayHelper::getValue($request->bodyParams, $this->accessTokenPath);

        if (empty($token) && $request->headers->has($this->accessTokenHeader)) {
            $token = $request->headers->get($this->accessTokenHeader);
        }

        $level = $this->getAuthLevel(\Yii::$app->requestedAction);

        if (!\Yii::$app->user->isGuest) {
            \Yii::$app->user->logout();
        }

        if ($level > self::AUTH_LEVEL_NONE && !empty($token)) {
            $identity = $user->loginByAccessToken($token);

            if ($level !== self::AUTH_LEVEL_NONE && !$identity) {
                throw new UnauthorizedHttpException('Incorrect or expired token provided');
            };
        }

        return $identity;
    }

    public function handleFailure($response)
    {
        if ($response->format != Response::FORMAT_HTML) {
            parent::handleFailure($response);
        }
    }

    /**
     * @param Action $action
     *
     * @return bool
     */
    public function getAuthLevel($action)
    {
        if ($this->isOptional($action)) {
            return self::AUTH_LEVEL_OPTIONAL;
        }

        return $this->isActive($action) ? self::AUTH_LEVEL_REQUIRED : self::AUTH_LEVEL_NONE;
    }
}