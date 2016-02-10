<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\components\auth;

use vm\core\ArrayObject;
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
    /**
     * @var array
     */
    public $accessTokenPath = ['token', 'hash'];

    /**
     * Authenticates the current user.
     *
     * @param \yii\web\User     $user
     * @param \yii\web\Request  $request
     * @param \yii\web\Response $response
     *
     * @return \yii\web\IdentityInterface the authenticated user identity. If authentication information is not
     *                                    provided, null will be returned.
     * @throws \yii\web\UnauthorizedHttpException if authentication information is provided but is invalid.
     */
    public function authenticate($user, $request, $response)
    {
        $identity = true;
        $json     = new ArrayObject(Json::decode($request->rawBody));

        /** @var ArrayObject $request */
        /** @noinspection PhpUndefinedFieldInspection */
        $request = $json->request;
        if (is_a($request, 'vm\core\ArrayObject')
            && $request->has(ArrayHelper::getValue($this->accessTokenPath, 0))
        ) {
            $token = $request;
            foreach ($this->accessTokenPath as $part) {
                $token = $token->{$part};
            }

            $identity = $user->loginByAccessToken($token);

            if (!$identity) {
                throw new UnauthorizedHttpException('Incorrect or expired token provided');
            }
        } else {
            if (!$user->isGuest) {
                $user->logout();
            }
        }

        return $identity;
    }
}