<?php

namespace Choijx\NowIpaySdk\Exceptions;

class Exception extends \Exception
{
    const UNKNOWN_ERROR = 9999;

    const INVALID_CONFIG = 1;

    const ERROR_GATEWAY = 2;

    const INVALID_SIGN = 3;

    const ERROR_BUSINESS = 4;

    /**
     * Raw error info.
     *
     * @var array
     */
    public $raw;

    /**
     * Bootstrap.
     *
     * @param string       $message
     * @param array|string $raw
     * @param int|string   $code
     */
    public function __construct($message = '', $raw = [], $code = self::UNKNOWN_ERROR)
    {
        $message = '' === $message ? 'Unknown Error' : $message;
        $this->raw = is_array($raw) ? $raw : [$raw];

        parent::__construct($message, intval($code));
    }
}
