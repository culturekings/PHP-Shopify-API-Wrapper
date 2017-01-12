<?php

namespace Shopify\Exception;

use Exception;

/**
 * Class FunctionNotFoundException
 * @package Shopify\Exception
 */
class FunctionNotFoundException extends Exception
{
    const MESSAGE_TEMPLATE = 'function %s does not exist';

    /**
     * MethodNotFoundException constructor.
     *
     * @param string $function The function that could not be found.
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($function, $code = 0, Exception $previous = null)
    {
        $message = sprintf(self::MESSAGE_TEMPLATE, $function);

        parent::__construct($message, $code, $previous);
    }
}
