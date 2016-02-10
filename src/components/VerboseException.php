<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\components;

use yii\base\Exception;

/**
 * Class VerboseException
 * @package vm\api\components
 */
class VerboseException extends Exception
{
    /**
     * @var string
     */
    public $template;

    /**
     * VerboseException constructor.
     *
     * @param string $template
     */
    public function __construct($template)
    {
        parent::__construct();
        $this->template = $template;
    }
}