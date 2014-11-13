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
    public $ServerVariables = array();

    /**
     * @var string
     */
    public $CustomerName;

    /**
     * @var string
     */
    public $UserName;

    public function __construct($appName)
    {
        $this->EnvironmentDetail = EnvironmentDetail::getInstance($appName);
        $this->WebRequestDetail = WebRequestDetail::getInstance();
        $this->ServerVariables = $this->getEnvironmentVariables();
    }

    private function getEnvironmentVariables()
    {
        return isset($_SERVER) ? WebRequestDetail::getRequestMap($_SERVER) : array();
    }

}