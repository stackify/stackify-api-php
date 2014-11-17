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
    protected $debug = false;

    const ERROR_CURL = 'Command returned an error. [Command: "%s"] [Return code: %d] [Message: "%s"]';
    const SUCCESS_CURL = 'Command sent. [Command: "%s"]';

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct($apiKey, $options);
        if ('Windows' === substr(php_uname(), 0, 7)) {
            throw new InitializationException('CurlTransport does not work on Windows');
        }
    }

    protected function getAllowedOptions()
    {
        return array(
            'curlPath' => '/.+/',
            'debug' => '/^(0|1)?$/',  // boolean
        );
    }

    protected function getTransportName()
    {
        return 'ExecTransport';
    }

    protected function send($data)
    {
        echo $data;
        // @TODO
        $url = Api::API_BASE_URL;
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
        if ($this->debug) {
            if ($result > 0) {
                // curl returned some error
                $this->logInternal(self::ERROR_CURL, $cmd, $result, implode(' ', $output));
            } else {
                $this->logInternal(self::SUCCESS_CURL, $cmd);
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