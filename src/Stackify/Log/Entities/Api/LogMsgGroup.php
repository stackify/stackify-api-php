<?php

namespace Stackify\Log\Entities\Api;

class LogMsgGroup
{

    /**
     * @var integer
     */
    public $CDID;

    /**
     * @var integer
     */
    public $CDAppID;

    /**
     * @var string
     */
    public $AppNameID;

    /**
     * @var string
     */
    public $AppEnvID;

    /**
     * @var integer
     */
    public $EnvID;

    /**
     * @var string
     */
    public $Env;

    /**
     * @var string
     */
    public $ServerName;

    /**
     * @var string
     */
    public $AppName;

    /**
     * @var string
     */
    public $AppLoc;

    /**
     * @var string
     */
    public $Logger;

    /**
     * @var string
     */
    public $Platform = 'PHP';

    /**
     * @var \Stackify\Log\Entities\Api\LogMsg[]
     */
    public $Msgs;

    /**
     * @param string $loggerName
     * @param string $appName
     * @param string $environmentName
     * @param \Stackify\Log\Entities\Api\LogMsg[] $logMsgs
     */
    public function __construct($loggerName, $appName, $environmentName, array $logMsgs)
    {
        $this->Logger = $loggerName;
        $this->AppName = $appName;
        $this->Env = $environmentName;
        $this->Msgs = $logMsgs;
        $environment = EnvironmentDetail::getInstance();
        $this->ServerName = $environment->DeviceName;
        $this->AppLoc = $environment->AppLocation;
    }

}