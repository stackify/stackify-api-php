<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\MessageBuilder;

class Layout extends \LoggerLayout
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    private $builder;

    public function __construct($appName = null)
    {
        $this->setAppName($appName);
    }

    public function setAppName($appName)
    {
        if ($appName) {
            $this->builder = new MessageBuilder('Stackify log4php v.1.0', $appName);
        }
    }

    public function format(\LoggerLoggingEvent $event)
    {
        $logEntry = new LogEntry($event);
        return $this->builder->getFormattedMessage($logEntry);
    }

}