<?php

namespace Stackify\Log\Entities;

/**
 * Entity representing native PHP errors (not OOP exceptions)
 * E.g. E_NOTICE or E_WARNING types with error details
 */
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

    public function getTrace()
    {
        $traceItem = array(
            'file' => $this->file,
            'line' => $this->line,
        );
        return array($traceItem);
    }

    public function getType()
    {
        switch ($this->code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }
        return 'Unknown PHP error';
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