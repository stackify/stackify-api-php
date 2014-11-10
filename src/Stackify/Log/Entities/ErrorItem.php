<?php

namespace Stackify\Log\Entities;

class ErrorItem
{
    /**
     * @var string
     */
    public $Message;

    /**
     * @var string
     */
    public $ErrorTypeCode;

    /**
     * @var array Key-value pairs
     */
    public $Data = array();

    /**
     * @var string
     */
    public $SourceMethod;

    /**
     * @var \Stackify\Log\Entities\TraceFrame[]
     */
    public $StackTrace = array();

    /**
     * @var \Stackify\Log\Entities\ErrorItem
     */
    public $InnerError;
}