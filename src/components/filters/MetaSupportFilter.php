<?php


namespace vr\api\components\filters;


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
     * @param Action $action
     * @return bool
     * @throws InvalidConfigException
     * @noinspection PhpMissingParamTypeInspection
     */
    public function beforeAction($action): bool
    {
        if (!($beforeAction = parent::beforeAction($action))) {
            return false;
        }

        if (!($meta = ArrayHelper::getValue(Yii::$app->request->bodyParams, 'meta'))) {
            return true;
        }

        if ($timezone = ArrayHelper::getValue($meta, $this->timezone)) {
            if (!is_array($this->db)) {
                $this->db = [$this->db];
            }

            foreach ($this->db as $db) {
                $connection = Yii::$app->get($db);

                $command = ArrayHelper::getValue([
                    'mysql' => 'set @@session.time_zone = "{0}"',
                    'pgsql' => 'set session time zone "{0}"',
                ], $connection->driverName);

                $connection->createCommand($command, [$timezone])->execute();
            }
        }

        Yii::$app->language = ArrayHelper::getValue($meta, $this->language) ?: Yii::$app->language;

        return true;
    }
}