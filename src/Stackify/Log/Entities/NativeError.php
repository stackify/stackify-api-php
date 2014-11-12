<?php

namespace Stackify\Log\Entities;

// @TODO add description
class NativeError
{

    private $code;
    private $message;
    private $file;
    private $line;

    public function __construct($code, $message, $file, $line)
    {
        $this->code = $code;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

    public static function getPHPErrorTypes()
    {
        return array(
            E_ERROR,
            E_RECOVERABLE_ERROR,
            E_WARNING,
            E_PARSE,
            E_NOTICE,
            E_STRICT,
            E_DEPRECATED,
            E_CORE_ERROR,
            E_CORE_WARNING,
            E_COMPILE_ERROR,
            E_COMPILE_WARNING,
            E_USER_ERROR,
            E_USER_WARNING,
            E_USER_NOTICE,
            E_USER_DEPRECATED,
        );
    }

}