<?php

namespace Stackify\Log\Entities\Agent;

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
        $this->ServerName = gethostname();
        $this->AppLoc = getcwd();
    }

}