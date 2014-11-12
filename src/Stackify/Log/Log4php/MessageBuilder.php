<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\MessageBuilder\AbstractBuilder;

class MessageBuilder extends AbstractBuilder
{

    protected function wrapLogEntry($logEvent)
    {
        return new LogEntry($logEvent);
    }

    protected function getLoggerName()
    {
        return 'Stackify log4php';
    }

    protected function getLoggerVersion()
    {
        return '1.0';
    }

}