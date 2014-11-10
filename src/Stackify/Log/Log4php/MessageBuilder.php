<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\Entities\LogMsg;
use Stackify\Log\MessageBuilder\AbstractBuilder;

class MessageBuilder extends AbstractBuilder
{

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    public function __construct()
    {
        parent::__construct();
        $this->timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
    }

    protected function getLoggerName()
    {
        return 'Stackify log4php';
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
        $datetime = new \DateTime();
        $datetime->setTimestamp($logEvent->getTimeStamp())->setTimezone($this->timezone);
        $logMsg = new LogMsg(
            (string) $logEvent->getLevel(),
            $logEvent->getMessage(),
            $datetime
        );
        $throwable = $logEvent->getThrowableInformation();
        if (null !== $throwable) {
            $exception = $throwable->getThrowable();
            $error = $this->createErrorFromException($datetime, $exception);
            $logMsg->Ex = $error;
            $logMsg->SrcLine = $exception->getLine();
            $logMsg->SrcMethod = $error->Error->SourceMethod;
        }
        return $logMsg;
    }

}