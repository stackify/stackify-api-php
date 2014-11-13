<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\MessageBuilder;

class Appender extends \LoggerAppender
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    private $builder;
    private $appName;
    private $environmentName;

    public function __construct($name = '', $appName = null, $environmentName = null)
    {
        parent::__construct($name);
        $this->setAppName($appName);
        $this->setEnvironmentName($environmentName);
    }

    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    public function setEnvironmentName($environmentName)
    {
        $this->environmentName = $environmentName;
    }

    protected function append(\LoggerLoggingEvent $event)
    {
        if (null === $this->builder) {
            $this->builder = new MessageBuilder('Stackify log4php v.1.0', $this->appName, $this->environmentName);
        }
        $logEntry = new LogEntry($event);
        echo $this->builder->getFormattedMessage($logEntry);
    }

}