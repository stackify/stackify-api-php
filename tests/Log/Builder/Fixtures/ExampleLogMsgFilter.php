<?php

namespace Stackify\Tests\Log\Builder\Fixtures;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Filters\LogMsgFilterable;

class ExampleLogMsgFilter implements LogMsgFilterable
{
    public function filter(LogMsg $logMsg)
    {
        $logMsg->setMessage('test filter');
        return $logMsg;
    }
}
