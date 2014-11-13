<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\MessageBuilder;

class Layout extends \LoggerLayout
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    private $builder;

    public function __construct()
    {
        $this->builder = new MessageBuilder('Stackify log4php', '1.0');
    }

    public function format(\LoggerLoggingEvent $event)
    {
        $logEntry = new LogEntry($event);
        return $this->builder->getFormattedMessage($logEntry);
    }

}