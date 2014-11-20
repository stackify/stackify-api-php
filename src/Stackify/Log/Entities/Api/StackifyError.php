<?php

namespace Stackify\Log\Entities\Api;

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

    public function __construct($appName, $environmentName)
    {
        $this->EnvironmentDetail = EnvironmentDetail::getInstance()
            ->init($appName, $environmentName);
        $this->WebRequestDetail = WebRequestDetail::getInstance();
        $this->ServerVariables = $this->getEnvironmentVariables();
    }

    private function getEnvironmentVariables()
    {
        return isset($_SERVER) ? WebRequestDetail::getRequestMap($_SERVER) : null;
    }

}