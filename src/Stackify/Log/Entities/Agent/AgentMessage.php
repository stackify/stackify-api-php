<?php

namespace Stackify\Log\Entities\Agent;

use Stackify\Log\Entities\Api\LogMsg;

class AgentMessage
{

    /**
     * @var \Stackify\Log\Entities\Agent\SenderInfo
     */
    public $SenderInfo;

    /**
     * @var \Stackify\Log\Entities\Api\LogMsg
     */
    public $Log;

    public function __construct($loggerName, $appName, $environmentName, LogMsg $logMsg)
    {
        $this->Log = $logMsg;
        $this->SenderInfo = new SenderInfo($loggerName, $appName, $environmentName);
    }

}