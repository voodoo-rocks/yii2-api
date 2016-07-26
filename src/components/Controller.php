<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\components;

use Exception;
use vm\api\components\auth\TokenAuth;
use vm\core\ArrayObject;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\log\Logger;
use yii\web\Response;

/**
 * Class Controller
 * @property ArrayObject request
 * @package vm\api\components
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @var bool
     */
    private $verbose = false;

    /**
     * @var ArrayObject
     */
    private $rawData = null;

    /**
     * @return array
     */
    public function behaviors()
    {
        $filters = [
            'authenticator'     => [
                'class' => TokenAuth::className(),
            ],
            'apiChecker'        => [
                'class' => ApiCheckerFilter::className(),
            ],
            'contentNegotiator' => [
                'class'   => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];

        return array_merge(parent::behaviors(), $filters);
    }

    /**
     * Runs the action
     *
     * @param string $id
     * @param array  $params
     *
     * @return array|mixed
     * @throws \yii\base\InvalidRouteException
     */
    public function runAction($id, $params = [])
    {
        try {

            // This is only for verbose mode
            $this->verbose = ArrayHelper::getValue($params, 'verbose', false);
            if ($this->verbose) {
                $action = $this->createAction($id);

                try {
                    $action->runWithParams([]);
                } catch (VerboseException $exception) {
                    return $exception->template;
                }

                return [];
            }

            // Getting the content of the request and transforms it to the structured data
            $this->rawData = new ArrayObject(Json::decode(Yii::$app->request->rawBody));

            // Execute the action
            $result = parent::runAction($id, $params);

            // Prepare the result
            if (!is_array($result)) {
                $result = [
                    'value' => $result,
                ];
            }

            if (ArrayHelper::getValue($this->rawData, ['header', 'unified'])) {
                if (count($result) == 1) {
                    $result = array_shift($result);
                }

                $result = ['data' => $result];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                       'result' => [
                           'succeeded' => true,
                       ],
                   ] + $result;
        } catch (\Exception $exception) {
            if (!$this->verbose) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'result' => [
                        'succeeded' => false,
                        'exception' => $this->prepareException($exception),
                    ],
                ];
            } else {
                return [
                    'exception' => $exception->getMessage()
                ];
            }
        }
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed            $result
     *
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        /** @noinspection PhpUndefinedFieldInspection */
        if (Yii::$app->api->enableProfiling) {
            list($count, $time) = Yii::getLogger()->getDbProfiling();

            $message = sprintf('Database queries executed: %d, total time: %f sec', $count, $time);
            Yii::getLogger()->log($message, Logger::LEVEL_PROFILE, 'database');

            Yii::endProfile($action->uniqueId);
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
        /** @noinspection PhpUndefinedFieldInspection */
        if (Yii::$app->api->enableProfiling) {
            Yii::beginProfile($action->uniqueId);
        }

        if (!parent::beforeAction($action)) {
            return false;
        };

        return true;
    }

    /**
     * Prepares occurred exception for delivering as a response
     *
     * @param Exception $exception
     *
     * @return array
     */
    private function prepareException($exception)
    {
        $attributes = [
            'name'    => (new \ReflectionClass($exception))->getShortName(),
            'message' => $exception->getMessage(),
            'code'    => $exception->getCode(),
        ];

        if (YII_DEBUG) {
            $attributes = array_merge($attributes, [
                'debug' => [
                    'file'  => $exception->getFile(),
                    'line'  => $exception->getLine(),
                    'trace' => explode(PHP_EOL, trim($exception->getTraceAsString()))
                ],
            ]);

            return $attributes;
        }

        return $attributes;
    }

    /**
     * @param array|callable $template
     *
     * @throws \vm\api\components\ParamsMismatchException
     * @throws \vm\api\components\VerboseException
     */
    public function checkInputParams($template = [])
    {

        if ($this->verbose) {
            if (is_callable($template)) {
                $template = call_user_func($template);
            }

            /** @noinspection PhpUndefinedFieldInspection */
            if (Yii::$app->api->requiresKey) {
                /** @noinspection PhpUndefinedFieldInspection */
                $template = ArrayHelper::merge(['key' => Yii::$app->api->randomKey], $template);
            }

            throw new VerboseException($template);
        }
    }

    /**
     * Gets the current request object
     * @return ArrayObject
     */
    public function getRequest()
    {
        return $this->rawData;
    }
}