<?php

namespace Stackify\Log\Builder;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Entities\Api\ErrorItem;
use Stackify\Log\Entities\Api\TraceFrame;
use Stackify\Log\Entities\Api\StackifyError;
use Stackify\Log\Entities\Api\EnvironmentDetail;
use Stackify\Log\Entities\ErrorWrapper;
use Stackify\Log\Entities\LogEntryInterface;
use Stackify\Log\Entities\Api\LogMsgGroup;
use Stackify\Log\Entities\Agent\AgentMessage;
use Stackify\Exceptions\InitializationException;

class MessageBuilder implements BuilderInterface
{

    protected $loggerName;
    protected $appName;
    protected $environmentName;

    public function __construct($loggerName, $appName, $environmentName = null)
    {
        if (!function_exists('json_encode')) {
            throw new InitializationException('JSON extension is required for Stackify logger');
        }
        $this->loggerName = $loggerName;
        $this->appName = $this->validateNotEmpty('AppName', $appName);
        $this->environmentName = $environmentName;
        // set state for environment details
        EnvironmentDetail::getInstance()->init($appName, $environmentName);
    }

    public function getAgentMessage(LogMsg $logMsg)
    {
        $message = new AgentMessage($this->loggerName, $this->appName, $this->environmentName, $logMsg);
        return $this->encodeJSON($message). PHP_EOL;
    }

    /**
     * @param \Stackify\Log\Entities\Api\LogMsg[] $logMsgs
     */
    public function getApiMessage(array $logMsgs)
    {
        $message = new LogMsgGroup(
            $this->loggerName,
            $this->appName,
            $this->environmentName,
            $logMsgs
        );
        return $this->encodeJSON($message);
    }

    /**
     * @return \Stackify\Log\Entities\Api\LogMsg
     */
    public function createLogMsg(LogEntryInterface $logEntry)
    {
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
            $error = new StackifyError($this->appName, $this->environmentName);
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
     * @return \Stackify\Log\Entities\Api\ErrorItem
     */
    protected function getErrorItem(ErrorWrapper $errorWrapper)
    {
        $errorItem = new ErrorItem();
        $errorItem->Message = $errorWrapper->getMessage();
        $errorItem->ErrorType = $errorWrapper->getType();
        $errorItem->ErrorTypeCode = $errorWrapper->getCode();
        $errorItem->SourceMethod = $errorWrapper->getSourceMethod();
        foreach ($errorWrapper->getTrace() as $index => $trace) {
            $errorItem->StackTrace[] = new TraceFrame(
                $trace['file'],
                $trace['line'],
                $trace['function']
            );
        }
        $previous = $errorWrapper->getInnerError();
        if ($previous) {
            $errorItem->InnerError = $this->getErrorItem($previous);
        }
        return $errorItem;
    }

    protected function encodeJSON($data)
    {
        return json_encode($data);
    }

    protected function validateNotEmpty($name, $value)
    {
        $result = trim($value);
        if (empty($result)) {
            throw new InitializationException("$name cannot be empty");
        }
        return $result;
    }

}