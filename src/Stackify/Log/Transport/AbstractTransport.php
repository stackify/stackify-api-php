<?php

namespace Stackify\Log\Transport;

use Stackify\Log\MessageBuilder;

abstract class AbstractTransport implements TransportInterface
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    protected $messageBuilder;
    private $errorLogPath;

    public function __construct()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->errorLogPath = realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'debug/error.log';
    }

    public function setMessageBuilder(MessageBuilder $messageBuilder)
    {
        $this->messageBuilder = $messageBuilder;
    }

    protected function logError($message)
    {
        $args = array_slice(func_get_args(), 1);
        $template = "[Stackify Log] $message [{$this->getTransportName()}]";
        $formatted = preg_replace('/\r\n/', '', vsprintf($template, $args));
        // first option - write to local file if possible
        // this can be not available because of file permissions
        @file_put_contents($this->errorLogPath, "$formatted\n", FILE_APPEND);
        // second option - send to default PHP error log
        error_log($formatted);
    }

    protected abstract function getTransportName();

}