<?php

namespace Stackify\Log\Monolog;

use Stackify\Log\MessageBuilder;

use Monolog\Logger;
use Monolog\Handler\AbstractHandler;

class Handler extends AbstractHandler
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    private $builder;

    public function __construct($appName, $environmentName, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->builder = new MessageBuilder('Stackify Monolog v.1.0', $appName, $environmentName);
    }

    public function handle(array $record)
    {
        $logEntry = new LogEntry($record);
        echo $this->builder->getAgentMessage($logEntry);
    }

}