<?php

/** @noinspection PhpMissingParamTypeInspection */

namespace vr\api\components\filters;

use DateTime;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;

/**
 * Class ExecutionInfoFilter
 * @package vr\api\components\filters
 */
class ExecutionInfoFilter extends ActionFilter
{
    /**
     * @var
     */
    private $_requestedAt;

    /**
     * @param Action $action
     * @return bool
     */
    public function beforeAction($action): bool
    {
        $this->_requestedAt = microtime(true);

        return parent::beforeAction($action);
    }

    /**
     * @param Action $action
     * @param mixed $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        if (is_array($result)) {
            $result += [
                '_execution' => [
                    'responseCode'   => Yii::$app->response->statusCode,
                    'responseStatus' => Yii::$app->response->statusText,
                    'timestamp'      => DateTime::createFromFormat('U', (int)$this->_requestedAt)
                        ->format('Y-m-d H:i:s'),
                    'executionTime'  => round(microtime(true) - $this->_requestedAt, 3),
                ]
            ];
        }

        return parent::afterAction($action, $result);
    }
}