<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Transport\Config\Api;
use Stackify\Exceptions\InitializationException;

/**
 * This transport collects log data until the end of processing.
 * It sends data executing shell curl and sending it to background.
 */
class ExecTransport extends AbstractApiTransport
{

    protected $curlPath = 'curl';

    const MAX_POST_LENGTH = 65536;  // 1024 * 64
    const ERROR_LENGTH = 'Batch is too long: %s';
    const ERROR_CURL = 'Command returned an error. [Command: "%s"] [Return code: %d] [Message: "%s"]';
    const SUCCESS_CURL = 'Command sent. [Command: "%s"]';

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct($apiKey, $options);
        if (!function_exists('exec')) {
            throw new InitializationException("PHP function 'exec' is not available, is it disabled for security reasons?");
        }
        if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            throw new InitializationException('Exec transport is not supposed to work on Windows');
        }
    }

    /**
     * Overrides parent's method
     */
    public function finish()
    {
        if (!empty($this->queue)) {
            // empty queue to avoid duplicates
            $queue = $this->queue;
            $this->queue = array();
            $this->sendChunk($queue);
        }
    }

    protected function getAllowedOptions()
    {
        return array_merge(parent::getAllowedOptions(), array(
            'curlPath' => '/.+/',
        ));
    }

    protected function getTransportName()
    {
        return 'ExecTransport';
    }

    protected function sendChunk(array $items)
    {
        $json = $this->messageBuilder->getApiMessage($items);
        $jsonLength = strlen($json);
        $count = count($items);
        if ($jsonLength > self::MAX_POST_LENGTH) {
            if (1 === $count) {
                // it makes no sense to divide into chunks, just fail
                $this->logError(self::ERROR_LENGTH, $jsonLength);
                return;
            }
            $maxCount = floor($count / ceil($jsonLength / self::MAX_POST_LENGTH));
            $chunks = array_chunk($items, $maxCount);
            foreach ($chunks as $chunk) {
                $this->sendChunk($chunk);
            }
        } else {
            $this->send($json);
        }
    }

    protected function send($data)
    {
        $url = Api::API_BASE_URL . Api::API_CALL_LOGS;
        $maxTime = Api::API_MAX_TIME;

        if ($this->agentConfig) {
            $url = $this->agentConfig->getApiBaseUrl() . $this->agentConfig->getApiCallLogsEndpoint();
            $maxTime = $this->agentConfig->getApiMaxTimeout();
        }


        $cmd = "$this->curlPath -X POST";
        foreach ($this->getApiHeaders() as $name => $value) {
            $cmd .= " --header \"$name: $value\"";
        }

        $escapedData = $this->escapeArg($data);
        
        $cmd .= " --data '$escapedData' '$url' --max-time $maxTime";
        if ($this->proxy) {
            $cmd .= " --proxy '$this->proxy'";
        }
        if ($this->getDebug()) {
            $cmd .= ' --verbose';
        } else {
            // return immediately while curl will run in the background
            $cmd .= ' > /dev/null 2>&1 &';
        }
        $output = array();
        $r = exec($cmd, $output, $result);
        // if debug mode is off, it makes no sense to check result,
        // because command is send to background
        if ($this->getDebug()) {
            if ($result !== 0) {
                // curl returned some error
                $this->logError(self::ERROR_CURL, $cmd, $result, implode(' ', $output));
            } else {
                $this->logDebug(self::SUCCESS_CURL, $cmd);
            }
        }
    }

    private function escapeArg($string)
    {
        // http://stackoverflow.com/a/1250279/871861
        return str_replace("'", "'\"'\"'", $string);
    }

}