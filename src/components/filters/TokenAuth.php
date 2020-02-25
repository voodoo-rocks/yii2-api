<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 28/10/2016
 * Time: 12:33
 */

namespace vr\api\components\filters;

use Yii;
use yii\base\Action;
use yii\filters\auth\AuthMethod;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

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
    public $type;
    /**
     * @var string
     */
    public $accessTokenPath = self::DEFAULT_TOKEN_PATH;

    /**
     * @var string
     */
    public $accessTokenHeader = self::DEFAULT_TOKEN_HEADER;

    /**
     * @var array
     */
    public $verbs = ['POST', 'DELETE', 'PUT'];

    /**
     * Authenticates the current user.
     *
     * @param User $user
     * @param Request $request
     * @param Response $response
     *
     * @return IdentityInterface the authenticated user identity. If authentication information is not
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

        $level = $this->getAuthLevel(Yii::$app->requestedAction);

        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        if ($level > self::AUTH_LEVEL_NONE && !empty($token)) {
            $identity = $user->loginByAccessToken($token, $this->type);

            if ($level !== self::AUTH_LEVEL_NONE && !$identity) {
                throw new UnauthorizedHttpException('Incorrect or expired token provided');
            }
        }

        return $identity;
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

    /**
     * @param $response
     *
     * @throws UnauthorizedHttpException
     */
    public function handleFailure($response)
    {
        if ($response->format != Response::FORMAT_HTML) {
            parent::handleFailure($response);
        }
    }

    protected function getActionId($action)
    {
        $parts = explode('/', $action->id);

        return ArrayHelper::getValue($parts, count($parts) - 1);
    }
}