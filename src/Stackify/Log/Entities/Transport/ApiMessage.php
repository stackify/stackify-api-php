<?php

namespace Stackify\Log\Entities\Transport;

use Stackify\Log\Entities\Api\LogMsg;

class ApiMessage
{

    /**
     * @var \Stackify\Log\Entities\Api\LogMsg[]
     */
    public $Log;

    /**
     * @var string
     */
    public $Logger;

    /**
     * @var string
     */
    public $AppName;

    /**
     * @var string
     */
    public $EnvironmentName;

    /**
     * @param string $loggerName
     * @param string $appName
     * @param string $environmentName
     * @param \Stackify\Log\Entities\Api\LogMsg[] $logMsgs
     */
    public function __construct($loggerName, $appName, $environmentName, array $logMsgs)
    {
        // @TODO modify when API will exist
        $this->Logger = $loggerName;
        $this->AppName = $appName;
        $this->EnvironmentName = $environmentName;
        $this->Log = $logMsgs;
    }

}