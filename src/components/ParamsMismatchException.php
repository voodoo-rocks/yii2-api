<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\components;

/**
 * Class ParamsMismatchException
 * @package vm\api\components
 */
class ParamsMismatchException extends \Exception
{
    /**
     * @var null|string
     */
    private $difference = null;

    /**
     * ParamsMismatchException constructor.
     *
     * @param string $difference
     */
    public function __construct($difference)
    {
        parent::__construct();

        $this->difference = $difference;
    }
}