<?php


namespace vr\api\components\filters;


use Exception;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
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
                $connection = Yii::$app->get($db);

                $command = ArrayHelper::getValue([
                    'mysql' => 'set @@session.time_zone = "{0}"',
                    'pgsql' => 'set session time zone "{0}"',
                ], $connection->driverName);

                $connection->createCommand(Yii::t('app', $command, [$timezone]))->execute();
            }
        }

        Yii::$app->language = ArrayHelper::getValue($meta, $this->language) ?: Yii::$app->language;

        return true;
    }
}