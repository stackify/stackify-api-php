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

    const ERROR_CURL = 'Command returned an error. [Command: "%s"] [Return code: %d] [Message: "%s"]';
    const SUCCESS_CURL = 'Command sent. [Command: "%s"]';

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct($apiKey, $options);
        if (!function_exists('exec')) {
            throw new InitializationException("PHP function 'exec' is not available, is it disabled for security reasons?");
        }
        // @TODO windows support
    }

    protected function getAllowedOptions()
    {
        return array(
            'curlPath' => '/.+/',
        );
    }

    protected function getTransportName()
    {
        return 'ExecTransport';
    }

    protected function send($data)
    {
        $url = Api::API_BASE_URL . Api::API_CALL_LOGS;
        $cmd = "$this->curlPath -X POST";
        foreach ($this->getApiHeaders() as $name => $value) {
            $cmd .= " --header \"$name: $value\"";
        }
        $escapedData = $this->escapeArg($data);
        $maxTime = Api::API_MAX_TIME;
        $cmd .= " --data '$escapedData' '$url' --max-time $maxTime";
        if ($this->proxy) {
            $cmd .= " --proxy '$this->proxy'";
        }
        if ($this->debug) {
            $cmd .= ' --verbose';
        } else {
            // return immediately while curl will run in the background
            $cmd .= ' > /dev/null 2>&1 &';
        }
        $output = array();
        $r = exec($cmd, $output, $result);
        // if debug mode is off, it makes no sense to check result,
        // because command is send to background
        if ($this->debug) {
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
        // @TODO test special chars
        // http://stackoverflow.com/a/1250279/871861
        return str_replace("'", "'\"'\"'", $string);
    }

}