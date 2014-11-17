<?php

namespace Stackify\Log\Transport;

use Stackify\Exceptions\InitializationException;

/**
 * Transport sends data using cURL PHP extension
 */
class CurlTransport extends AbstractApiTransport
{

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct($apiKey, $options);
        if (!function_exists('curl_init')) {
            throw new InitializationException('cURL PHP extension is not available');
        }
    }

    protected function getAllowedOptions()
    {
        return array();
    }

    protected function getTransportName()
    {
        return 'CurlTransport';
    }

    protected function send($data)
    {
        echo $data;
    }

}