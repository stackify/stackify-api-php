<?php

namespace Stackify\Log\Filters;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Entities\Api\ErrorItem;
use Stackify\Log\Entities\Api\StackifyError;

class ErrorGovernor
{

    const MAX_DUPLICATES = 100;

    private $counter = array();

    public function shouldBeSent(LogMsg $logMsg = null)
    {
        if (!isset($logMsg->Ex)) {
            return true;
        }
        $errorItem = $this->getBaseError($logMsg->Ex);
        $key = $this->getUniqueKey($errorItem);
        if (!isset($this->counter[$key])) {
            $this->counter[$key] = 0;
        }
        return (++$this->counter[$key] <= self::MAX_DUPLICATES);
    }

    /**
     * @return \Stackify\Log\Entities\Api\ErrorItem
     */
    private function getBaseError(StackifyError $error)
    {
        $errorItem = $error->Error;
        while (null !== $errorItem->InnerError) {
            $errorItem = $errorItem->InnerError;
        }
        return $errorItem;
    }

    private function getUniqueKey(ErrorItem $item)
    {
        $key = sprintf('%s-%s-%s', $item->ErrorType, $item->ErrorTypeCode, $item->SourceMethod);
        return md5($key);
    }

}