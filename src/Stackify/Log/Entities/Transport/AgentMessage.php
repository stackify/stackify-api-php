<?php

namespace Stackify\Log\Entities\Transport;

use Stackify\Log\Entities\Api\LogMsg;

class AgentMessage
{

    /**
     * @var \Stackify\Log\Entities\Transport\SenderInfo
     */
    public $SenderInfo;

    /**
     * @var \Stackify\Log\Entities\Api\LogMsg
     */
    public $Log;

    public function __construct($loggerName, $appName, LogMsg $logMsg)
    {
        $this->Log = $logMsg;
        $this->SenderInfo = new SenderInfo($loggerName, $appName);
    }

}