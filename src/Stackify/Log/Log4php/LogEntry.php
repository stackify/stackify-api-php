<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\Entities\LogEntryInterface;

final class LogEntry implements LogEntryInterface
{

    /**
     * @var \LoggerLoggingEvent
     */
    private $logEvent;

    /**
     * @var \DateTime
     */
    private $datetime;

    public function __construct(\LoggerLoggingEvent $logEvent)
    {
        $this->logEvent = $logEvent;
        $timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        $this->datetime = new \DateTime();
        $this->datetime->setTimestamp($logEvent->getTimeStamp())
            ->setTimezone($timezone);
    }

    public function getContext()
    {
        // is not supported by log4php
        return null;
    }

    public function getException()
    {
        $throwable = $this->logEvent->getThrowableInformation();
        if (null !== $throwable) {
            return $throwable->getThrowable();
        }
    }

    public function getLevel()
    {
        return (string) $this->logEvent->getLevel();
    }

    public function getMessage()
    {
        return $this->logEvent->getMessage();
    }

    public function getMilliseconds()
    {
        return $this->datetime->getTimestamp() * 1000;
    }

    public function getNativeError()
    {
        // log5php logger does not support native errors
        return null;
    }

    public function isErrorLevel()
    {
        $errorLevel = \LoggerLevel::getLevelError();
        return $this->logEvent->getLevel()->isGreaterOrEqual($errorLevel);
    }

    public function getBacktrace()
    {
        // @TODO check if nesting level was changed
        return array_slice(debug_backtrace(), 10);
    }

}