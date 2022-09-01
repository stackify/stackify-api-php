<?php

namespace Stackify\Log\Filters;

use Stackify\Log\Entities\Api\LogMsg;

/**
 * Log message filterable interface
 */
interface LogMsgFilterable
{  
    /**
     * Allows filtering of log messages before uploading
     *
     * @param LogMsg $logMsg Log message instance
     *
     * @return LogMsg
     */
    public function filter(LogMsg $logMsg);
}