<?php

namespace Stackify\Log\Transport;

use Stackify\Log\MessageBuilder;

abstract class AbstractTransport implements TransportInterface
{

    /**
     * @var \Stackify\Log\MessageBuilder
     */
    protected $messageBuilder;
    private $debugLogPath;
    protected $debug = false;

    public function __construct()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->debugLogPath = realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'debug/log.log';
    }

    public function setMessageBuilder(MessageBuilder $messageBuilder)
    {
        $this->messageBuilder = $messageBuilder;
    }

    protected abstract function getTransportName();

    protected function logError($message)
    {
        $this->log($message, func_get_args(), false);
    }

    protected function logDebug($message)
    {
        if (!$this->debug) {
            return;
        }
        $this->log($message, func_get_args(), true);
    }

    private function log($message, $args, $success = true)
    {
        $replacements = array_slice($args, 1);
        $prefix = $success ? 'Stackify Log' : 'Stackify Error';
        $template = "[$prefix] $message [{$this->getTransportName()}]";
        $formatted = preg_replace('/\r\n/', '', vsprintf($template, $replacements));
        // first option - write to local file if possible
        // this can be not available because of file permissions
        @file_put_contents($this->debugLogPath, "$formatted\n", FILE_APPEND);
        if (!$success) {
            // second option - send to default PHP error log
            error_log($formatted);
        }
    }

}