<?php

namespace Stackify\Log\Monolog;

use Stackify\Log\MessageBuilder;

use Monolog\Formatter\FormatterInterface;

class Formatter implements FormatterInterface
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    private $builder;

    public function __construct()
    {
        $this->builder = new MessageBuilder('Stackify Monolog', '1.0');
    }

    public function format(array $record)
    {
        $logEntry = new LogEntry($record);
        return $this->builder->getFormattedMessage($logEntry);
    }

    public function formatBatch(array $records)
    {
        throw new \BadMethodCallException('Multiple messages processing is not supported');
    }

}