<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\MessageBuilder\AbstractBuilder;

class MessageBuilder extends AbstractBuilder
{

    protected function getLoggerName()
    {
        return 'Stackify PHP Logger';
    }

    protected function getLoggerVersion()
    {
        return '1.0';
    }

    /**
     * @return \Stackify\Log\Entities\Api\LogMsg
     */
    protected function createLogMsg($logEvent)
    {
        $logMsg = new LogMsg(
            $logEvent['level'],
            $logEvent['message'],
            $logEvent['datetime']
        );
        $context = $logEvent['context'];
        $exception = $this->popException($context);
        if (null !== $exception) {
            $error = $this->createErrorFromException($logEvent['datetime'], $exception);
            $logMsg->setError($error);
        }
        if (!empty($context)) {
            $logMsg->data = $this->encodeJSON($context);
        }
        return $logMsg;
    }

}