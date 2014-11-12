<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\MessageBuilder\AbstractBuilder;

class MessageBuilder extends AbstractBuilder
{

    protected function wrapLogEntry($logEvent) {
        return new LogEntry($logEvent);
    }

    protected function getLoggerName()
    {
        return 'Stackify PHP Logger';
    }

    protected function getLoggerVersion()
    {
        return '1.0';
    }

}