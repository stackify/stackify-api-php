<?php

namespace Stackify\Tests\Log\Builder\Fixtures;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Filters\LogMsgFilterable;

class ExampleLogMsgFilterWithStackTrace implements LogMsgFilterable
{
    public function filter(LogMsg $logMsg)
    {
        $logMsg->setMessage(
            'Stack trace:\\n : SomeExampleFunction'.$logMsg->getMessage().'(\''.$logMsg->getMessage().'\')'
        );
        return $logMsg;
    }
}
