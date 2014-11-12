<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\Entities\LogEntryInterface;

class LogEntry implements LogEntryInterface
{

    private $record;
    private $exception;
    private $context;

    public function __construct(array $record) {
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
        if (!empty($context)) {
            $this->context = $context;
        }
    }

    public function getContext() {
        return $this->context;
    }

    public function getException() {
        return $this->exception;
    }

    public function getLevel() {
        return $this->record['level'];
    }

    public function getMessage() {
        return $this->record['message'];
    }

    public function getMilliseconds() {
        return $this->record['datetime']->getTimestamp() * 1000;
    }

    public function getNativeError() {
        // standalone logger does not support native errors
        return null;
    }

}