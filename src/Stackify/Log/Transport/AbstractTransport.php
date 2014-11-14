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

    protected function logError($message)
    {
        $args = array_slice(func_get_args(), 1);
        $template = "[{$this->getTransportName()}] $message";
        $formatted = preg_replace('/\r\n/', '', vsprintf($template, $args));
        // @TODO implement
        echo "$formatted\n";
    }

    protected abstract function getTransportName();

}