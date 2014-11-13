<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\MessageBuilder;
use Stackify\Log\Transport\TransportInterface;
use Stackify\Log\Transport\AgentTransport;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @var \Stackify\Log\Transport\TransportInterface
     */
    private $transport;

    public function __construct($appName, $environmentName, TransportInterface $transport = null)
    {
        $this->timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        $messageBuilder = new MessageBuilder('Stackify PHP Logger v.1.0', $appName, $environmentName);
        if (null === $transport) {
            $transport = new AgentTransport($messageBuilder);
        }
        $this->transport = $transport;
    }

    public function log($level, $message, array $context = array())
    {
        $logEvent = array(
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'datetime' => new \DateTime('now', $this->timezone),
        );
        $this->transport->addEntry(new LogEntry($logEvent));
    }

}