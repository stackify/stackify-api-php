<?php

namespace Stackify\Log\Monolog;

use Stackify\Log\Entities\LogMsg;
use Stackify\Log\Entities\ErrorItem;
use Stackify\Log\Entities\TraceFrame;
use Stackify\Log\Entities\StackifyError;
use Stackify\Log\MessageBuilder\AbstractBuilder;

class MessageBuilder extends AbstractBuilder
{

    protected function getLoggerName()
    {
        return 'Stackify Monolog';
    }

    protected function getLoggerVersion()
    {
        return '1.0';
    }

    /**
     * @return \Stackify\Log\Entities\LogMsg
     */
    protected function createLogMsg($logEvent)
    {
        $logMsg = new LogMsg(
            $logEvent['level_name'],
            $logEvent['message'],
            $logEvent['datetime']
        );
        $context = $logEvent['context'];
        $exception = $this->popException($context);
        if (null !== $exception) {
            $error = $this->createErrorFromException($logEvent['datetime'], $exception);
            $logMsg->setError($error);
        } elseif ($this->isPHPError($context)) {
            $error = $this->createError($logEvent);
            $logMsg->setError($error);
        }
        if (!empty($context)) {
            $logMsg->data = $this->encodeJSON($context);
        }
        return $logMsg;
    }

    private function isPHPError(array $context)
    {
        // four fields must be defined: code, message, file, line
        // also code must be within predefined constants
        return isset($context['code'], $context['message'], $context['file'], $context['line'])
            && in_array($context['code'], $this->getPHPErrorTypes());
    }

    private function getPHPErrorTypes()
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

    /**
     * Create StackifyError object from native PHP error (E_NOTICE, E_WARNING, etc.)
     * @return \Stackify\Log\Entities\StackifyError
     */
    private function createError(array $record)
    {        
        $context = $record['context'];
        $error = new StackifyError();
        $error->OccurredEpochMillis = $record['datetime']->getTimestamp() * 1000;
        $errorItem = new ErrorItem();
        $errorItem->Message = $context['message'];
        $errorItem->ErrorTypeCode = $context['code'];
        $errorItem->StackTrace[] = new TraceFrame(
            $context['file'],
            $context['line'],
            null // method is not defined in native error
        );
        $error->Error = $errorItem;
        return $error;
    }

}