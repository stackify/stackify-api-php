<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\Standalone\MessageBuilder;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @var \Stackify\Log\MessageBuilder\BuilderInterface
     */
    private $builder;

    public function __construct()
    {
        // @TODO support minimal level
        $this->builder = new MessageBuilder();
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
        $logEvent['formatted'] = $this->builder->getFormattedMessage($logEvent);
        $this->write($logEvent);
    }

    protected function write($logEvent)
    {
        // @TODO this is stub implementation
        echo $logEvent['formatted'];
    }

}