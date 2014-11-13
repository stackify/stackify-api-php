<?php

namespace Stackify\Log\Transport;

use Stackify\Log\MessageBuilder;

abstract class AbstractTransport implements TransportInterface
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    protected $messageBuilder;

    public function __construct(MessageBuilder $messageBuilder)
    {
        $this->messageBuilder = $messageBuilder;
    }

}