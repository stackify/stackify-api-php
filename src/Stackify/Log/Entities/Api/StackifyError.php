<?php

namespace Stackify\Log\Entities\Api;

use Stackify\Log\Transport\Config\Agent;

class StackifyError
{
    /**
     * @var null Environment details are not known by PHP loggers
     */
    public $EnvironmentDetail;

    /**
     * @var integer Milliseconds
     */
    public $OccurredEpochMillis;

    /**
     * @var \Stackify\Log\Entities\Api\ErrorItem
     */
    public $Error;

    /**
     * @var \Stackify\Log\Entities\Api\WebRequestDetail
     */
    public $WebRequestDetail;

    /**
     * @var array Key-value pairs
     */
    public $ServerVariables;

    /**
     * @var string
     */
    public $CustomerName;

    /**
     * @var string
     */
    public $UserName;

    public function __construct($appName, $environmentName, $logServerVariables = false)
    {
        $this->EnvironmentDetail = EnvironmentDetail::getInstance()
            ->init($appName, $environmentName);
        $this->WebRequestDetail = WebRequestDetail::getInstance();

        if ($logServerVariables) {
            $this->ServerVariables = $this->getEnvironmentVariables();
        }
    }

    /**
     * Get server environment variables
     *
     * @return array
     */
    private function getEnvironmentVariables()
    {
        $agentConfig = Agent::getInstance();
        if ($agentConfig) {
            return isset($_SERVER) && $agentConfig->getCaptureServerVariables() 
                ? WebRequestDetail::getRequestMap($_SERVER, $agentConfig->getCaptureServerVariablesBlacklist(), $agentConfig->getCaptureServerVariablesWhitelist())
                : null;
        }
        return isset($_SERVER) ? WebRequestDetail::getRequestMap($_SERVER, null, array('*')) : null;
    }

}
