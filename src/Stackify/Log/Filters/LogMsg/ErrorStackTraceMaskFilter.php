<?php

namespace Stackify\Log\Filters\LogMsg;

use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Filters\LogMsgFilterable;

/**
 * Error Mask argument filter
 */
class ErrorStackTraceMaskFilter implements LogMsgFilterable
{
    /**
     * Allows filtering of log messages before uploading
     *
     * @param LogMsg $logMsg Log message instance
     *
     * @return LogMsg
     */
    public function filter(LogMsg $logMsg)
    {
        if (!$logMsg->hasError()) {
            return $logMsg;
        }

        if (stripos($logMsg->getMessage(), "stack trace:\n") === false) {
            return $logMsg;
        }

        $logMsg->setMessage(
            $this->mask($logMsg->getMessage())
        );

        return $logMsg;
    }

    /**
     * Masked php trace
     *
     * @param string $message Raw message
     *
     * @return void
     */
    protected function mask($message)
    {
        if (!is_string($message)) {
            return $message;
        }

        try {
            $delimeter = "\n";
            $hasObject = strpos($message, 'Object(') !== false; // Checks if we can detect an object(
            $messageParts = explode($delimeter, $message);
            
            $patternObjectString = "/Object\\(([\\\w\\\\]|[^\\)])+\\)/";
            $pattern = "/(.*:(?:.*))(\\((?:\\'.*\\'|[\\d]|[\\w]|,|\\s|\\(|\\)|\\\\)+\\))/";
            $replacement = '$1()';
            $maskedList = array();

            foreach ($messageParts as $part) {
                $newString = $part;
                if ($hasObject) {
                    $newString = preg_replace($patternObjectString, '', $newString);
                }
                $maskedList[] = preg_replace($pattern, $replacement, $newString);
            }

            return implode($delimeter, $maskedList);
        } catch (\Exception $ex) {
            return $message;
        }
    }
}