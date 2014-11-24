<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Builder\BuilderInterface;
use Stackify\Log\Entities\LogEntryInterface;

interface TransportInterface
{
    public function setMessageBuilder(BuilderInterface $messageBuilder);
    public function addEntry(LogEntryInterface $logEntry);
    public function finish();
}