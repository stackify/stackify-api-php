<?php

namespace Stackify\Log\MessageBuilder;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Entities\Api\ErrorItem;
use Stackify\Log\Entities\Api\TraceFrame;
use Stackify\Log\Entities\Api\StackifyError;
use Stackify\Log\Entities\NativeError;
use Stackify\Exceptions\InitializationException;

abstract class AbstractBuilder implements BuilderInterface
{

    public function __construct()
    {
        if (!function_exists('json_encode')) {
            throw new InitializationException('JSON extension is required for Stackify logger');
        }
        // @TODO for all children gather stacktrace even if exception is not available
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
        $milliseconds = $logEntry->getMilliseconds();
        $logMsg = new LogMsg(
            $logEntry->getLevel(),
            $logEntry->getMessage(),
            $milliseconds
        );
        $exception = $logEntry->getException();
        if ($exception = $logEntry->getException()) {
            $error = $this->createErrorFromException($milliseconds, $exception);
            $logMsg->setError($error);
        } elseif ($nativeError = $logEntry->getNativeError()) {
            $error = $this->createErrorFromNativeError($milliseconds, $nativeError);
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
    protected function getErrorItem(\Exception $exception)
    {
        $errorItem = new ErrorItem();
        $errorItem->Message = $exception->getMessage();
        $errorItem->ErrorTypeCode = $exception->getCode();
        foreach ($exception->getTrace() as $index => $trace) {
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
        $previous = $exception->getPrevious();
        if ($previous) {
            // @TODO limit recurrence?
            $errorItem->InnerError = $this->getErrorItem($previous);
        }
        return $errorItem;
    }

    /**
     * @return \Stackify\Log\Entities\Api\StackifyError
     */
    protected function createErrorFromException($milliseconds, \Exception $exception)
    {
        $error = new StackifyError();
        $error->OccurredEpochMillis = $milliseconds;
        $error->Error = $this->getErrorItem($exception);
        return $error;
    }

    /**
     * Create StackifyError object from native PHP error (E_NOTICE, E_WARNING, etc.)
     * @return \Stackify\Log\Entities\Api\StackifyError
     */
    private function createErrorFromNativeError($milliseconds, NativeError $nativeError)
    {
        $error = new StackifyError();
        $error->OccurredEpochMillis = $milliseconds;
        $errorItem = new ErrorItem();
        $errorItem->Message = $nativeError->getMessage();
        $errorItem->ErrorTypeCode = $nativeError->getCode();
        $errorItem->StackTrace[] = new TraceFrame(
            $nativeError->getFile(),
            $nativeError->getLine(),
            null // method is not defined in native error
        );
        $error->Error = $errorItem;
        return $error;
    }

    protected function encodeJSON($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

}