<?php

namespace Stackify\Log\Entities\Api;

class ErrorItem
{

    /**
     * @var string
     */
    public $Message;

    /**
     * @var string
     */
    public $ErrorType;

    /**
     * @var string
     */
    public $ErrorTypeCode;

    /**
     * @var array Key-value pairs
     */
    public $Data;

    /**
     * @var string
     */
    public $SourceMethod;

    /**
     * @var \Stackify\Log\Entities\Api\TraceFrame[]
     */
    public $StackTrace = array();

    /**
     * @var \Stackify\Log\Entities\Api\ErrorItem
     */
    public $InnerError;

}