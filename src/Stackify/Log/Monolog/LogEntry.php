<?php

namespace Stackify\Log\Monolog;

use Stackify\Log\Entities\LogEntryInterface;
use Stackify\Log\Entities\NativeError;

use Monolog\Logger as MonologLogger;

final class LogEntry implements LogEntryInterface
{

    private $record;
    private $exception;
    private $context;
    private $nativeError;

    public function __construct(array $record)
    {
        $this->record = $record;
        $context = $record['context'];
        // find exception and remove from context
        foreach ($context as $key => $value) {
            if ($value instanceof \Exception) {
                $this->exception = $value;
                unset($context[$key]);
                break;
            }
        }
        if ($this->isNativeError($context)) {
            $this->nativeError = new NativeError(
                $context['code'],
                $context['message'],
                $context['file'],
                $context['line']
            );
            unset(
                $context['code'],
                $context['message'],
                $context['file'],
                $context['line']
            );
        }
        if (!empty($context)) {
            $this->context = $context;
        }
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getLevel()
    {
        return $this->record['level_name'];
    }

    public function getMessage()
    {
        return $this->record['message'];
    }

    public function getMilliseconds()
    {
        return round($this->record['datetime']->format('Uu') / 1000);
    }

    public function getNativeError() 
    {
        return $this->nativeError;
    }

    private function isNativeError(array $context)
    {
        // four fields must be defined: code, message, file, line
        // also code must be within predefined constants
        return isset($context['code'], $context['message'], $context['file'], $context['line'])
            && in_array($context['code'], NativeError::getPHPErrorTypes());
    }

    public function isErrorLevel()
    {
        return $this->record['level'] >= MonologLogger::ERROR;
    }

}