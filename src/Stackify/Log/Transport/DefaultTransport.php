<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Entities\LogEntryInterface;

/**
 * 
 */
class DefaultTransport extends AbstractTransport
{

    private $initialized = false;
    private $handle;

    public function addEntry(LogEntryInterface $logEntry)
    {
        $data = $this->messageBuilder->getAgentMessage($logEntry);
    }

    public function flush()
    {
        // do nothing, agent trasport does not use queues
    }

    protected function send($data)
    {
        if (!$this->initialized) {
            $this->handle = fsockopen($hostname, $port, $errno, $errstr, $timeout);
        }
        fwrite($this->handle, $data);
    }

}