<?php


namespace vr\api\components\filters;


use Exception;
use Throwable;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * Class MetaSupportFilter
 * @package vr\api\components\filters
 */
class MetaSupportFilter extends ActionFilter
{
    /**
     * @var string|array
     */
    public $db = 'db';

    /**
     * @var string
     */
    public $timezone = 'timezone';

    /**
     * @var string
     */
    public $language = 'locale';

    /**
     * @var string
     */
    public $defaultTimezone = null;

    /**
     * @param Action $action
     * @return bool
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $meta = ArrayHelper::getValue(Yii::$app->request->bodyParams, 'meta');

        if ($timezone = ArrayHelper::getValue($meta, $this->timezone) ?: $this->defaultTimezone) {
            if (!is_array($this->db)) {
                $this->db = [$this->db];
            }

            foreach ($this->db as $db) {
                /** @var Connection $connection */
                $connection = Yii::$app->get($db);

                $command = ArrayHelper::getValue([
                    'mysql' => function ($timezone) {
                        return strtr('set @@session.time_zone = "{0}"', ['{0}' => $timezone]);
                    },
                    'pgsql' => function ($timezone) {
                        if (count($parts = explode(':',$timezone)) > 1) {
                            $timezone = (int)$parts[1] * 60 + (int)$parts[0];
                        }
                        return strtr('set session time zone "{0}"', ['{0}' => $timezone]);
                    },
                ], $connection->driverName, fn() => null);

                if ($command = call_user_func($command, $timezone)) {
                    $connection->createCommand($command)->execute();
                }
            }

            try {
                date_default_timezone_set($timezone);
            } catch (Throwable $throwable) {
            }
        }

        Yii::$app->language = ArrayHelper::getValue($meta, $this->language) ?: Yii::$app->language;

        return true;
    }
}