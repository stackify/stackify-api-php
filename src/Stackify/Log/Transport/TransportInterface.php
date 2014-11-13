<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Entities\LogEntryInterface;

interface TransportInterface
{
    public function addEntry(LogEntryInterface $logEntry);
    public function flush();
}