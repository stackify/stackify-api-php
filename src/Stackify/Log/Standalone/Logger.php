<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\MessageBuilder;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    private $builder;

    public function __construct($appName)
    {
        $this->builder = new MessageBuilder('Stackify PHP Logger v.1.0', $appName);
        $this->timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
    }

    public function log($level, $message, array $context = array())
    {
        $logEvent = array(
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'datetime' => new \DateTime('now', $this->timezone),
        );
        $logEntry = new LogEntry($logEvent);
        // @TODO refactor here
        $logEvent['formatted'] = $this->builder->getFormattedMessage($logEntry);
        $this->write($logEvent);
    }

    protected function write($logEvent)
    {
        // @TODO this is stub implementation
        echo $logEvent['formatted'];
    }

}