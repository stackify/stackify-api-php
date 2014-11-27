<?php

namespace Stackify\Log\Builder;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Entities\LogEntryInterface;

/**
 * Empty interface implementation
 */
class NullBuilder implements BuilderInterface
{

    public function createLogMsg(LogEntryInterface $logEntry) {}

    public function getAgentMessage(LogMsg $logMsg) {}

    public function getApiMessage(array $logMsgs) {}

}