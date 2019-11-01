<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api\components;

use vr\api\components\filters\ApiCheckerFilter;
use vr\api\components\filters\TokenAuth;
use vr\api\doc\components\DocAction;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\OptionsAction;
use yii\web\Response;

/**
 * Class Controller
 * @package vr\api\components
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @var array
     */
    public $authExcept = [];

    /**
     * @var null
     */
    public $authOnly = null;

    /**
     * @var array
     */
    public $authOptional = [];

    /**
     * @var bool
     */
    public $isAtomic = true;

    /**
     * @var
     */
    public $requestedAt;

    /**
     * @var bool
     */
    public $includeExecInfo = true;

    /**
     * @var bool
     */
    protected $verbose = false;

    /**
     * @return array
     */
    public function behaviors()
    {
        $filters = [
            'verbs'             => [
                'class'   => VerbFilter::class,
                'actions' => [
                    '*' => ['post', 'options', 'get'],
                ],
            ],
            'cors'              => [
                'class' => Cors::class,
            ],
            'contentNegotiator' => [
                'class'   => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml'  => Response::FORMAT_XML,
                    'text/html'        => Response::FORMAT_HTML,
                ],
            ],
            'apiChecker'        => [
                'class' => ApiCheckerFilter::class,
            ],
        ];

        $definitions = \Yii::$app->getComponents(true);
        $setUp       = ArrayHelper::getValue($definitions, ['user', 'identityClass']);

        if (!empty($setUp)) {
            $filters = array_merge($filters, [
                'rateLimiter' => [
                    'class' => RateLimiter::class,
                ],
            ]);
        }

        if (Yii::$app->request->isPost || $this->verbose) {
            $filters = array_merge($filters, [
                'authenticator' => [
                    'class'    => TokenAuth::class,
                    'except'   => $this->authExcept,
                    'only'     => $this->authOnly,
                    'optional' => $this->authOptional,
                ],
            ]);
        }

        return $filters;
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed            $result
     *
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if ($this->isAtomic
            && Yii::$app->has('db')
            && ($transaction = Yii::$app->db->getTransaction())) {
            $transaction->commit();
        }

        return $result;
    }

    /**
     * Makes necessary preparation before the action. In this case it sets up the appropriate response format
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    function beforeAction($action)
    {
        $this->requestedAt = microtime(true);

        if (!parent::beforeAction($action)) {
            return false;
        };

        if ($this->isAtomic && Yii::$app->has('db')) {
            Yii::$app->db->beginTransaction();
        }

        return true;
    }

    /**
     * @param string $id
     *
     * @return null|ApiAction|DocAction|\yii\base\Action|OptionsAction
     */
    public function createAction($id)
    {
        if (Yii::$app->request->isOptions) {
            return new OptionsAction($id, $this);
        }

        if (Yii::$app->request->isGet && !$this->verbose) {
            return new DocAction($this->uniqueId . '/' . $id, $this);
        }

        if (Yii::$app->request->isPost) {
            $methodName = 'action' . str_replace(' ', '',
                    ucwords(str_replace('-', ' ', $id)));

            return new ApiAction($id, $this, $methodName);
        }

        return parent::createAction($id);
    }

    /**
     * @param $action
     *
     * @return null|array
     * @throws \yii\base\InvalidConfigException
     */
    public function getActionParams($action)
    {
        $this->verbose = true;
        $action        = $this->createAction($action);

        try {
            $action->runWithParams([]);
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (VerboseException $exception) {
            return $exception->params;
        }

        return null;
    }

    /**
     * @param $callable
     *
     * @return bool
     * @throws VerboseException
     */
    protected function checkInputParams($callable = null)
    {
        if ($this->verbose) {
            $params = [];

            if ($callable) {
                $params = call_user_func($callable);
            }

            throw new VerboseException($params);
        }

        return true;
    }
}