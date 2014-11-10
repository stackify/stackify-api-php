<?php

namespace Stackify\Log\Entities;

class StackifyError
{
    /**
     * @var null Environment details are not known by PHP loggers
     * @TODO is it really unknown?
     */
    public $EnvironmentDetail;

    /**
     * @var integer Milliseconds
     */
    public $OccurredEpochMillis;

    /**
     * @var \Stackify\Log\Entities\ErrorItem
     */
    public $Error;

    /**
     * @var \Stackify\Log\Entities\WebRequestDetail
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

    public function __construct()
    {
        $this->WebRequestDetail = new WebRequestDetail();
        $this->ServerVariables = $this->getEnvironmentVariables();
    }

    private function getEnvironmentVariables()
    {
        return isset($_ENV) ? $this->WebRequestDetail->getRequestMap($_ENV) : array();
    }

}