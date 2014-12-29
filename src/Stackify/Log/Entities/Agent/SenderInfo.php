<?php

namespace Stackify\Log\Entities\Agent;

use Stackify\Log\Entities\Api\EnvironmentDetail;

class SenderInfo
{

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

    public function __construct($loggerName, $appName, $environmentName)
    {
        $this->Logger = $loggerName;
        $this->AppName = $appName;
        $this->Env = $environmentName;
        $environment = EnvironmentDetail::getInstance();
        $this->ServerName = $environment->DeviceName;
        $this->AppLoc = $environment->AppLocation;
    }

}