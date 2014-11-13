<?php

namespace Stackify\Log\MessageBuilder;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Entities\Api\ErrorItem;
use Stackify\Log\Entities\Api\TraceFrame;
use Stackify\Log\Entities\Api\StackifyError;
use Stackify\Log\Entities\ErrorWrapper;
use Stackify\Exceptions\InitializationException;


abstract class AbstractBuilder implements BuilderInterface
{

    public function __construct()
    {
        if (!function_exists('json_encode')) {
            throw new InitializationException('JSON extension is required for Stackify logger');
        }
    }

    public function getFormattedMessage($logEvent)
    {
        // @TODO add sender info
        $logMsg = $this->createLogMsg($logEvent);
        return $this->encodeJSON($logMsg). PHP_EOL;
    }

    /**
     * @return \Stackify\Log\Entities\Api\LogMsg
     */
    protected function createLogMsg($logEvent)
    {
        $logEntry = $this->wrapLogEntry($logEvent);
        $logMsg = new LogMsg(
            $logEntry->getLevel(),
            $logEntry->getMessage(),
            $logEntry->getMilliseconds()
        );
        $errorWrapper = null;
        if ($exception = $logEntry->getException()) {
            $errorWrapper = new ErrorWrapper($exception);
        } elseif ($nativeError = $logEntry->getNativeError()) {
            $errorWrapper = new ErrorWrapper($nativeError);
        } elseif ($logEntry->isErrorLevel()) {
            $errorWrapper = new ErrorWrapper($logEntry);
        }
        if (null !== $errorWrapper) {
            $error = new StackifyError();
            $error->OccurredEpochMillis = $logEntry->getMilliseconds();
            $error->Error = $this->getErrorItem($errorWrapper);
            $logMsg->setError($error);
        }
        if (null !== $logEntry->getContext()) {
            $logMsg->data = $this->encodeJSON($logEntry->getContext());
        }
        return $logMsg;
    }

    /**
     * Wrap log event that comes from third-party logger to an object with common interface
     * @return \Stackify\Log\Entities\LogEntryInterface
     */
    protected abstract function wrapLogEntry($logEvent);

    /**
     * Returns logger name (will be visible in OpsManager)
     * @return string
     */
    protected abstract function getLoggerName();

    /**
     * Returns logger version (string) (will be visible in OpsManager)
     * @return string
     */
    protected abstract function getLoggerVersion();

    /**
     * @return \Stackify\Log\Entities\Api\ErrorItem
     */
    protected function getErrorItem(ErrorWrapper $errorWrapper)
    {
        $errorItem = new ErrorItem();
        $errorItem->Message = $errorWrapper->getMessage();
        $errorItem->ErrorType = $errorWrapper->getType();
        $errorItem->ErrorTypeCode = $errorWrapper->getCode();
        foreach ($errorWrapper->getTrace() as $index => $trace) {
            $errorItem->StackTrace[] = new TraceFrame(
                $trace['file'],
                $trace['line'],
                $trace['function']
            );
            if (0 === $index) {
                // first record in stack trace has method
                $errorItem->SourceMethod = $trace['function'];
            }
        }
        $previous = $errorWrapper->getInnerError();
        if ($previous) {
            // @TODO limit recurrence?
            $errorItem->InnerError = $this->getErrorItem($previous);
        }
        return $errorItem;
    }

    protected function encodeJSON($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

}