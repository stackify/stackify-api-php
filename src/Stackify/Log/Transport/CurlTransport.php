<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Transport\Config\Api;
use Stackify\Exceptions\InitializationException;

/**
 * Transport sends data using cURL PHP extension.
 */
class CurlTransport extends AbstractApiTransport
{

    const ERROR_CURL = 'Curl returned an error. [Error no: %d] [Message: "%s"] [HTTP code: %d]';
    const SUCCESS_CURL = 'Curl sent data successfully';

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
        // @TODO
        $headers = array();
        foreach ($this->getApiHeaders() as $name => $value) {
            $headers[] = "$name: $value";
        }
        $url = Api::API_BASE_URL;
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_TIMEOUT, Api::API_MAX_TIME);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        if ($this->proxy) {
            curl_setopt($handle, CURLOPT_PROXY, $this->proxy);
        }
        curl_exec($handle);
        $errorNo = curl_errno($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        if (0 !== $errorNo) {
            $this->logInternal(self::ERROR_CURL, $errorNo, $error, $code);
        } elseif ($this->debug) {
            $this->logInternal(self::SUCCESS_CURL);
        }
        curl_close($handle);
    }

}