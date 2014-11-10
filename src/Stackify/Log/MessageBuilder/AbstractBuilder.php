<?php

namespace Stackify\Log\MessageBuilder;

use Stackify\Log\Entities\ErrorItem;
use Stackify\Log\Entities\TraceFrame;
use Stackify\Exceptions\InitializationException;

abstract class AbstractBuilder implements BuilderInterface
{

    /**
     * Structured data is a part of RFC5424
     * Placeholders mean logger name + logger version
     * @link https://tools.ietf.org/html/rfc5424#page-15
     */
    const TEMPLATE_SD_DATA = '[STACKIFY_LOG][SENDER_INFO LOGGER_NAME="%s v.%s"]';

    public function __construct()
    {
        if (!function_exists('json_encode')) {
            throw new InitializationException('JSON extension is required for Stackify logger');
        }
    }

    public function getSyslogMessage($logEvent)
    {
        $logMsg = $this->createLogMsg($logEvent);
        return $this->getStructuredData() . ' ' . $this->encodeJSON($logMsg);
    }

    /**
     * @return \Stackify\Log\Entities\LogMsg
     */
    protected abstract function createLogMsg($logEvent);

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
     * @return \Stackify\Log\Entities\ErrorItem
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
            $errorItem->InnerError = $this->getErrorItem($previous);
        }
        return $errorItem;
    }

    protected function encodeJSON($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    private function getStructuredData()
    {
        return sprintf(self::TEMPLATE_SD_DATA,
            $this->getLoggerName(), $this->getLoggerVersion());
    }

}