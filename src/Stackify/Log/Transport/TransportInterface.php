<?php

namespace Stackify\Log\Transport;

use Stackify\Log\MessageBuilder;
use Stackify\Log\Entities\LogEntryInterface;

interface TransportInterface
{
    public function setMessageBuilder(MessageBuilder $messageBuilder);
    public function addEntry(LogEntryInterface $logEntry);
    public function flush();
}