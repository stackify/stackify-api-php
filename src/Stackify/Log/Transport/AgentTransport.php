<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Entities\LogEntryInterface;

class AgentTransport extends AbstractTransport
{

    public function addEntry(LogEntryInterface $logEntry)
    {
        echo $this->messageBuilder->getFormattedMessage($logEntry);
    }

    public function flush()
    {
        
    }

}