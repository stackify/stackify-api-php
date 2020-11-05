<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\Builder\MessageBuilder;
use Stackify\Log\Transport\TransportInterface;
use Stackify\Log\Transport\AgentSocketTransport;
use Stackify\Log\Transport\Config\Agent;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    /**
     * @var \Stackify\Log\Transport\TransportInterface
     */
    private $transport;

    public function __construct($appName, $environmentName = null, TransportInterface $transport = null, $logServerVariables = false, $config = null)
    {
        $messageBuilder = new MessageBuilder('Stackify PHP Logger v.1.0', $appName, $environmentName, $logServerVariables);
        if (null === $transport) {
            $transport = new AgentSocketTransport();
        }
        if (null !== $config) {
            Agent::getInstance()->extract($config);
        }
        $transport->setMessageBuilder($messageBuilder);
        $this->transport = $transport;
    }

    public function __destruct()
    {
        $this->transport->finish();
    }

    public function log($level, $message, array $context = array())
    {
        $logEvent = array(
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'milliseconds' => round(microtime(true) * 1000),
        );
        $this->transport->addEntry(new LogEntry($logEvent));
    }

}
