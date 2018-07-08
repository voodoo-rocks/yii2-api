<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/07/2018
 * Time: 22:28
 */

namespace vr\api\src\components;

/**
 * Class OptionsAction
 * @package vr\api\src\components
 */
class OptionsAction extends yii\rest\OptionsAction
{
    /**
     * @var array
     */
    public $headers = ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Authorization'];

    /**
     * @param null $id
     */
    public function run($id = null)
    {
        parent::run();

        $headers->set('Access-Control-Allow-Headers', implode(', ', $this->headers));
    }
}