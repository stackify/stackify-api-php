<?php

namespace Stackify\Log\Monolog;

use Stackify\Log\Monolog\MessageBuilder;

use Monolog\Formatter\FormatterInterface;

class Formatter implements FormatterInterface
{

    /**
     * @var \Stackify\Log\MessageBuilder\BuilderInterface
     */
    private $builder;

    public function __construct()
    {
        $this->builder = new MessageBuilder();
    }

    public function format(array $record)
    {
        return $this->builder->getFormattedMessage($record);
    }

    public function formatBatch(array $records)
    {
        throw new \BadMethodCallException('Multiple messages processing is not supported');
    }

}