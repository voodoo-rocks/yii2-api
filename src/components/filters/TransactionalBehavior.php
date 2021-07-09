<?php


namespace vr\api\components\filters;


use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Transaction;

/**
 * Class TransactionalBehavior
 * @package namespace vr\api\components\filters
 */
class TransactionalBehavior extends ActionFilter
{
    /**
     * @var string|null
     */
    public ?string $isolationLevel = null;

    /**
     * @var string|array
     */
    public $db = 'db';

    /**
     * @var Transaction[]
     */
    private array $_transactions = [];

    /**
     * @param Action $action
     * @return bool
     * @throws InvalidConfigException
     */
    public function beforeAction($action): bool
    {
        $configs = is_array($this->db) ? $this->db : [$this->db];

        foreach ($configs as $config) {
            if (Yii::$app->get($config, false)) {
                $this->_transactions[] = Yii::$app->get($config)->transaction ?: Yii::$app->get($config)->beginTransaction($this->isolationLevel);
            }
        }


        return parent::beforeAction($action);
    }

    /**
     * @param Action $action
     * @param mixed $result
     * @return mixed
     * @throws Exception
     */
    public function afterAction($action, $result)
    {
        if ($this->_transactions) {
            foreach ($this->_transactions as $transaction)
                $transaction->commit();
        }

        return parent::afterAction($action, $result);
    }
}