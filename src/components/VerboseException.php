<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/10/2016
 * Time: 20:11
 */

namespace vr\api\components;

use yii\base\Exception;

/**
 * Class VerboseException
 * @package vr\api\components
 */
class VerboseException extends Exception
{
    /**
     * @var array
     */
    public $params;

    /**
     * VerboseException constructor.
     *
     * @param array $params
     */
    public function __construct($params)
    {
        $this->params = $params;
        parent::__construct('Getting action params. Please never use this exception', null, null);
    }
}