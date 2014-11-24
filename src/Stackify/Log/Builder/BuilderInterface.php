<?php

namespace Stackify\Log\Builder;

use Stackify\Log\Entities\LogEntryInterface;

interface BuilderInterface
{

    /**
     * @return string  Formatted JSON
     */
    public function getAgentMessage(LogEntryInterface $logEntry);

    /**
     * @param \Stackify\Log\Entities\Api\LogMsg[] $logMsgs
     * @return string  Formatted JSON
     */
    public function getApiMessage(array $logMsgs);

    /**
     * @return \Stackify\Log\Entities\Api\LogMsg
     */
    public function createLogMsg(LogEntryInterface $logEntry);

}