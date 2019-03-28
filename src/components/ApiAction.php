<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 30/01/2019
 * Time: 22:06
 */

namespace vr\api\components;

use vr\core\ErrorsException;
use yii\base\Exception;
use yii\base\InlineAction;
use yii\base\UserException;
use yii\web\HttpException;

/**
 * Class ApiAction
 * @package vr\api\components
 */
class ApiAction extends InlineAction
{
    /**
     * @var int
     */
    public $validationFailedStatusCode = 400;

    /**
     * @param array $params
     *
     * @return array|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function runWithParams($params)
    {
        try {
            $data = parent::runWithParams($params);

            return array_merge(['success' => true], $data ?: []);
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (HttpException $e) {
            \Yii::$app->response->statusCode = $e->statusCode;

            return $this->convertExceptionToArray($e);
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (ErrorsException $e) {
            \Yii::$app->response->statusCode = $this->validationFailedStatusCode;

            return $this->convertExceptionToArray($e, $e->data);
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (UserException $e) {
            \Yii::$app->response->statusCode = $this->validationFailedStatusCode;

            return $this->convertExceptionToArray($e);
        }
    }

    /**
     * @param Exception $e
     *
     * @return array
     */
    protected function convertExceptionToArray($e, $data = []): array
    {
        return [
            'success'   => false,
            'exception' => $array = [
                'name'    => 'Exception',
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'data'    => $data,
            ],
        ];
    }
}