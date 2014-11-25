<?php

namespace Stackify\Log\Builder;

use Stackify\Log\Entities\LogEntryInterface;

/**
 * Empty interface implementation
 */
class NullBuilder implements BuilderInterface
{

    public function createLogMsg(LogEntryInterface $logEntry) {}

    public function getAgentMessage(LogEntryInterface $logEntry) {}

    public function getApiMessage(array $logMsgs) {}

}