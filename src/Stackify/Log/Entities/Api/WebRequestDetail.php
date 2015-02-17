<?php

namespace Stackify\Log\Entities\Api;

use Stackify\Utils\TypeConverter;

class WebRequestDetail
{

    const HIDDEN_VALUE = 'X-MASKED-X';

    /**
     * @var string
     */
    public $UserIPAddress;

    /**
     * @var string
     */
    public $HttpMethod;

    /**
     * @var string
     */
    public $RequestProtocol;

    /**
     * @var string
     */
    public $RequestUrl;

    /**
     * @var string
     */
    public $RequestUrlRoot;

    /**
     * @var string
     */
    public $ReferralUrl;

    /**
     * @var array Key-value pairs
     */
    public $Headers;

    /**
     * @var array Key-value pairs
     */
    public $Cookies;

    /**
     * @var array Key-value pairs
     */
    public $QueryString;

    /**
     * @var array Key-value pairs
     */
    public $PostData;

    /**
     * @var array Key-value pairs
     */
    public $SessionData;

    /**
     * @var string
     */
    public $PostDataRaw;

    /**
     * @var string
     */
    public $MVCAction;

    /**
     * @var string
     */
    public $MVCController;

    /**
     * @var string
     */
    public $MVCArea;

    private function __construct()
    {
        $this->UserIPAddress = $this->getIpAddress();
        $this->HttpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $this->RequestProtocol = $this->getProtocol();
        $this->RequestUrl = $this->getRequestUrl();
        $this->RequestUrlRoot = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $this->ReferralUrl = filter_input(INPUT_SERVER, 'HTTP_REFERER');
        $this->Headers = $this->getHeaders();
        $this->Cookies = isset($_COOKIE) ? self::getRequestMap($_COOKIE, true) : null;
        $this->QueryString = isset($_GET) ? self::getRequestMap($_GET) : null;
        $this->PostData = isset($_POST) ? self::getRequestMap($_POST) : null;
        $this->SessionData = isset($_SESSION) ? self::getRequestMap($_SESSION, true) : null;
        $this->PostDataRaw = file_get_contents('php://input');
    }

    /**
     * Singleton attributes
     */
    private function __clone() {}

    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Converts $data to key-value pairs, where values are strings
     * @param mixed $data
     * @param boolean $maskValues  Hide request values
     * @return array
     */
    public static function getRequestMap($data, $maskValues = false)
    {
        $result = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $result[$key] = $maskValues
                    ? self::HIDDEN_VALUE
                    : TypeConverter::stringify($value);
            }
        }
        return empty($result) ? null : $result;
    }

    private function getIpAddress()
    {
        $keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );
        foreach ($keys as $key) {
            $ip = filter_input(INPUT_SERVER, $key);
            if (null !== $ip && false !== filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        return null;
    }

    private function getProtocol()
    {
        $protocol = null;
        if ('cli' === php_sapi_name()) {
            $protocol = 'CLI';
        } else {
            $protocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');
        }
        return $protocol;
    }

    private function getRequestUrl()
    {
        $https = filter_input(INPUT_SERVER, 'HTTPS');
        $ssl = null !== $https && 'off' !== $https;
        $protocol = $ssl ? 'https' : 'http';
        list($url,) = explode('?', filter_input(INPUT_SERVER, 'REQUEST_URI'));
        $serverName = filter_input(INPUT_SERVER, 'SERVER_NAME');
        if ($serverName && $url) {
            return "$protocol://$serverName" . $url;
        }
        return null;
    }

    private function getHeaders()
    {
        $headers = array();
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (false === $headers) {
                // getallheaders() returns false in case of error
                $headers = array();
            }
        }
        foreach ($headers as $name => $value) {
            if ('cookie' === strtolower($name)) {
                $headers[$name] = self::HIDDEN_VALUE;
            }
        }
        return empty($headers) ? null : $headers;
    }

}