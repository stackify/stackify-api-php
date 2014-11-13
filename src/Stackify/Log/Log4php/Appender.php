<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\MessageBuilder;

class Appender extends \LoggerAppender
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    private $builder;

    public function __construct($name = '', $appName = null)
    {
        parent::__construct($name);
        $this->setAppName($appName);
    }

    public function setAppName($appName)
    {
        // @TODO what if appName was not passed?
        if ($appName) {
            $this->builder = new MessageBuilder('Stackify log4php v.1.0', $appName);
        }
    }

    protected function append(\LoggerLoggingEvent $event)
    {
        $logEntry = new LogEntry($event);
        echo $this->builder->getFormattedMessage($logEntry);
    }

}