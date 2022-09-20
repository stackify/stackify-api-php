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
use Stackify\Log\Filters\LogMsg\ErrorStackTraceMaskFilter;
use Stackify\Log\Filters\LogMsgFilterable;
use Stackify\Log\Transport\Config\Agent;

class MessageBuilder implements BuilderInterface
{

    protected $loggerName;
    protected $appName;
    protected $environmentName;
    protected $logServerVariables;
    /**
     * Log message filters
     *
     * @var LogMsgFilterable[]
     */
    protected $logMessageFilters;
    /**
     * Agent config
     *
     * @var Agent
     */
    protected $agentConfig;

    public function __construct($loggerName, $appName, $environmentName = null, $logServerVariables = false, $config = null)
    {
        if (!function_exists('json_encode')) {
            throw new InitializationException('JSON extension is required for Stackify logger');
        }
        $this->loggerName = $loggerName;
        $this->appName = $this->validateNotEmpty('AppName', $appName);
        $this->environmentName = $environmentName;
        $this->logServerVariables = $logServerVariables;
        // set state for environment details
        EnvironmentDetail::getInstance()->init($appName, $environmentName);
        $this->agentConfig = !empty($config) ? $config : Agent::getInstance();
        $this->logMessageFilters = array();

        $this->addLogMessageFiltersFromConfig();
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
            $error = new StackifyError($this->appName, $this->environmentName, $this->logServerVariables);
            $error->OccurredEpochMillis = $logEntry->getMilliseconds();
            $error->Error = $this->getErrorItem($errorWrapper);
            $logMsg->setError($error);
        }

        if (null !== $logEntry->getContext()) {
            $logMsg->data = $this->encodeJSON($logEntry->getContext());
        }

        return $this->filterMessage($logMsg);
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

        $errorWrapperTrace = $errorWrapper->getTrace();
        if ($errorWrapperTrace && is_array($errorWrapperTrace)) {
            foreach ($errorWrapper->getTrace() as $index => $trace) {
                $errorItem->StackTrace[] = new TraceFrame(
                    $trace['file'],
                    $trace['line'],
                    $trace['function']
                );
            }
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

    /**
     * Filter log message
     *
     * @param LogMsg $logMsg Log message instance
     *
     * @return LogMsg
     */
    protected function filterMessage(LogMsg $logMsg)
    {
        if (count($this->logMessageFilters)) {
            foreach ($this->logMessageFilters as $filterable) {
                $logMsg = $filterable->filter($logMsg);
            }
        }

        return $logMsg;
    }

    /**
     * Add log message filter
     *
     * @param string $filterClass Filter class string
     *
     * @return void
     */
    protected function addLogMessageFilter($filterClass)
    {
        if (!is_string($filterClass)) {
            return;
        }

        if (!class_exists($filterClass)) {
            return;
        }

        if (!is_subclass_of($filterClass, LogMsgFilterable::class)) {
            return;
        }

        $this->logMessageFilters[$filterClass] = new $filterClass();
    }

    /**
     * Add log message filters from config
     *
     * @return void
     */
    public function addLogMessageFiltersFromConfig()
    {
        if (!$this->agentConfig) {
            return;
        }

        $configLogMessageFilters = $this->agentConfig->getLogMessageFilters();
        if ($configLogMessageFilters && count($configLogMessageFilters)) {
            foreach ($configLogMessageFilters as $filterClass) {
                $this->addLogMessageFilter($filterClass);
            }
        }

        if ($this->agentConfig->isMaskErrorStackTraceArguments()) {
            $this->addLogMessageFilter(ErrorStackTraceMaskFilter::class);
        }
    }

    /**
     * Get log message filters
     *
     * @return LogMsgFilterable[]
     */
    public function getLogMessageFilters()
    {
        return $this->logMessageFilters;
    }
}
