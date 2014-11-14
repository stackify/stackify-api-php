<?php

namespace Stackify\Log\Entities\Transport;

class SenderInfo
{

    /**
     * @var string
     */
    public $Logger;

    /**
     * @var string
     */
    public $AppName;

    public function __construct($loggerName, $appName)
    {
        $this->Logger = $loggerName;
        $this->AppName = $appName;
    }

}