<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api\components;

use Exception;
use vr\api\components\filters\ExecutionInfoFilter;
use vr\api\components\filters\TokenAuth;
use vr\api\doc\components\DocAction;
use vr\core\components\TransactionalBehavior;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
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
    protected $verbose = false;

    /**
     * @return array
     * @throws Exception
     */
    public function behaviors(): array
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
            'info'              => [
                'class' => ExecutionInfoFilter::class,
            ],
            'contentNegotiator' => [
                'class'   => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml'  => Response::FORMAT_XML,
                    'text/html'        => Response::FORMAT_HTML,
                ],
            ],
            'transactional'     => [
                'class' => TransactionalBehavior::class,
            ]
        ];

        $definitions = Yii::$app->getComponents(true);

        if (ArrayHelper::getValue($definitions, ['user', 'identityClass'])) {
            $filters = array_merge($filters, [
                'rateLimiter' => [
                    'class' => RateLimiter::class,
                ],
            ]);
        }

        // TODO: refactor
        if (Yii::$app->request->isPost || $this->verbose) {
            $filters = array_merge($filters, [
                'authenticator' => [
                    'class'       => CompositeAuth::class,
                    'except'      => $this->authExcept,
                    'only'        => $this->authOnly,
                    'optional'    => $this->authOptional,
                    'authMethods' => [
                        [
                            'class' => TokenAuth::class,
                        ],
                        [
                            'class' => HttpBearerAuth::class,
                        ]
                    ]
                ],
            ]);
        }

        return $filters;
    }

    /**
     * @param $action
     *
     * @return null|array
     * @throws InvalidConfigException
     */
    public function getActionParams($action): ?array
    {
        $this->verbose = true;
        $action        = $this->createAction($action);

        try {
            $action->runWithParams([]);
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (VerboseException $exception) {
            return YII_DEBUG ? $exception->params : $this->anonymizeParams($exception->params);
        }

        return null;
    }

    /**
     * @param string $id
     *
     * @return null|ApiAction|DocAction|Action|OptionsAction
     * @noinspection PhpMissingParamTypeInspection
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
     * @param array|object|null $params
     * @return null
     */
    private function anonymizeParams(?array $params): ?array
    {
        if ($params === null) {
            return null;
        }

        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    $params[$key] = $this->anonymizeParams($value);
                } else {
                    $params[$key] = null;
                }
            }
        }

        return $params;
    }

    /**
     * @param $callable
     *
     * @return bool
     * @throws VerboseException
     */
    protected function checkInputParams($callable = null): bool
    {
        if ($this->verbose) {
            $params = [];

            if ($callable) {
                $params = call_user_func($callable);
                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                if (@$this->module->purifyDoc) {
                    $params = $this->purify($params);
                }
            }

            throw new VerboseException($params);
        }

        return true;
    }

    /**
     * @param array $params
     * @return array
     */
    private function purify(array $params): array
    {
        foreach ($params as $key => $value) {
            $params[$key] = is_array($value) ? $this->purify($value) : null;
        }
        return $params;
    }
}