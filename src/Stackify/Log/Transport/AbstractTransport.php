<?php

namespace Stackify\Log\Transport;

use Stackify\Log\MessageBuilder;

abstract class AbstractTransport implements TransportInterface
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    protected $messageBuilder;

    public function setMessageBuilder(MessageBuilder $messageBuilder)
    {
        $this->messageBuilder = $messageBuilder;
    }

}