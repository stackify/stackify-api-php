<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Transport\Config\Api;
use Stackify\Exceptions\InitializationException;

/**
 * Transport sends data using cURL PHP extension.
 */
class CurlTransport extends AbstractApiTransport
{

    const ERROR_CURL = 'Curl returned an error. [Error no: %d] [HTTP code: %d] [Message: "%s"] [Response: "%s"]';
    const SUCCESS_CURL = 'Curl sent data successfully. [HTTP code: %d] [Response: "%s"]';

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct($apiKey, $options);
        if (!function_exists('curl_init')) {
            throw new InitializationException('cURL PHP extension is not available');
        }
    }

    protected function getTransportName()
    {
        return 'CurlTransport';
    }

    protected function send($data)
    {
        $headers = array();
        foreach ($this->getApiHeaders() as $name => $value) {
            $headers[] = "$name: $value";
        }

        $url = Api::API_BASE_URL . Api::API_CALL_LOGS;
        $maxTimeout = Api::API_MAX_TIME;

        if ($this->agentConfig) {
            $url = $this->agentConfig->getApiBaseUrl() . $this->agentConfig->getApiCallLogsEndpoint();
            $maxTimeout = $this->agentConfig->getApiMaxTimeout();
        }
        
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_TIMEOUT, $maxTimeout);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        if ($this->proxy) {
            curl_setopt($handle, CURLOPT_PROXY, $this->proxy);
        }
        $response = curl_exec($handle);
        $errorNo = curl_errno($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        if (0 !== $errorNo || 200 !== $code) {
            $this->logError(self::ERROR_CURL, $errorNo, $code, $error, $response);
        } else {
            $this->logDebug(self::SUCCESS_CURL, $code, $response);
        }
        curl_close($handle);
    }

}