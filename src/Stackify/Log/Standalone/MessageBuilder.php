<?php

namespace Stackify\Log\Standalone;

use Stackify\Log\Entities\LogMsg;
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
     * @return \Stackify\Log\Entities\LogMsg
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
            $logMsg->Ex = $error;
            $logMsg->SrcLine = $exception->getLine();
            $logMsg->SrcMethod = $error->Error->SourceMethod;
        }
        if (!empty($context)) {
            $logMsg->data = $this->encodeJSON($context);
        }
        return $logMsg;
    }

}