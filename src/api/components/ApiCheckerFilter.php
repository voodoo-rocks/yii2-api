<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\components;

use Yii;
use yii\base\ActionFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class ApiCheckerFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (Yii::$app->api->requiresKey) {

            /** @var \ArrayObject $this->owner->request */
            $apiKey = ArrayHelper::getValue($this->owner->request, 'key');
            if (!$apiKey || !in_array($apiKey, \Yii::$app->api->keys)) {

                throw new BadRequestHttpException('Missing or invalid API key');
            }
        }

        return true;
    }
}