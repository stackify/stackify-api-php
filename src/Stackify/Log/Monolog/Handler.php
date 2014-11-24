<?php

namespace Stackify\Log\Monolog;

use Stackify\Log\Builder\MessageBuilder;
use Stackify\Log\Transport\TransportInterface;
use Stackify\Log\Transport\AgentTransport;

use Monolog\Logger;
use Monolog\Handler\AbstractHandler;

class Handler extends AbstractHandler
{

    /**
     * @var \Stackify\Log\Transport\TransportInterface
     */
    private $transport;

    public function __construct($appName, $environmentName = null, TransportInterface $transport = null, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $messageBuilder = new MessageBuilder('Stackify Monolog v.1.0', $appName, $environmentName);
        if (null === $transport) {
            $transport = new AgentTransport();
        }
        $transport->setMessageBuilder($messageBuilder);
        $this->transport = $transport;
    }

    public function handle(array $record)
    {
        $logEntry = new LogEntry($record);
        $this->transport->addEntry($logEntry);
    }

    public function close()
    {
        parent::close();
        $this->transport->finish();
    }

}