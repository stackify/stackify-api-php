<?php

namespace Stackify\Log\Entities\Api;

use Stackify\Utils\TypeConverter;
use Stackify\Log\Transport\Config\Agent;

class WebRequestDetail
{

    const HIDDEN_VALUE = 'X-MASKED-X';
    private static $_HIDDEN_HEADERS = array(
        'cookie' => 1,
        'authorization' => 1
    );

    /**
     * User IP address
     *
     * @var string
     */
    public $UserIPAddress;

    /**
     * HTTP Method
     *
     * @var string
     */
    public $HttpMethod;

    /**
     * Request Protocol
     *
     * @var string
     */
    public $RequestProtocol;

    /**
     * Request URL
     *
     * @var string
     */
    public $RequestUrl;

    /**
     * Request URL Root
     *
     * @var string
     */
    public $RequestUrlRoot;

    /**
     * Referral URL
     *
     * @var string
     */
    public $ReferralUrl;

    /**
     * Request Headers
     *
     * @var array Key-value pairs
     */
    public $Headers;

    /**
     * Request Cookies
     *
     * @var array Key-value pairs
     */
    public $Cookies;

    /**
     * $_GET values
     *
     * @var array Key-value pairs
     */
    public $QueryString;

    /**
     * $_POST values
     *
     * @var array Key-value pairs
     */
    public $PostData;

    /**
     * $_SESSION values
     *
     * @var array Key-value pairs
     */
    public $SessionData;

    /**
     * Raw post data
     *
     * @var string
     */
    public $PostDataRaw;

    /**
     * MVC Action
     *
     * @var string
     */
    public $MVCAction;

    /**
     * MVC Controller
     *
     * @var string
     */
    public $MVCController;

    /**
     * MVC Area
     *
     * @var string
     */
    public $MVCArea;

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->UserIPAddress = $this->getIpAddress();
        $this->HttpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $this->RequestProtocol = $this->getProtocol();
        $this->RequestUrl = $this->getRequestUrl();
        $this->RequestUrlRoot = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $this->ReferralUrl = filter_input(INPUT_SERVER, 'HTTP_REFERER');

        /**
         * @var \Stackify\Log\Transport\Config\Agent
         */
        $agentConfig = Agent::getInstance();

        if ($agentConfig) {
            $this->Headers = $agentConfig->getCaptureErrorHeaders()
                ? $this->getHeaders(
                    $agentConfig->getCaptureErrorHeadersBlacklist(),
                    $agentConfig->getCaptureErrorHeadersWhitelist()
                )
                : null;
            $this->Cookies = isset($_COOKIE) && $agentConfig->getCaptureErrorCookies()
                ? self::getRequestMap(
                    $_COOKIE,
                    $agentConfig->getCaptureErrorCookiesBlacklist(),
                    $agentConfig->getCaptureErrorCookiesWhitelist()
                )
                : null;

            $this->QueryString = isset($_GET) && $agentConfig->getCaptureGetVariables()
                ? self::getRequestMap(
                    $_GET,
                    $agentConfig->getCaptureGetVariablesBlacklist(),
                    $agentConfig->getCaptureGetVariablesWhitelist()
                )
                : null;
            $this->PostData = isset($_POST) && $agentConfig->getCapturePostVariables()
                ? self::getRequestMap(
                    $_POST,
                    $agentConfig->getCapturePostVariablesBlacklist(),
                    $agentConfig->getCapturePostVariablesWhitelist()
                )
                : null;
            $this->SessionData = isset($_SESSION) && $agentConfig->getCaptureSessionVariables()
                ? self::getRequestMap(
                    $_SESSION,
                    $agentConfig->getCaptureSessionVariablesBlacklist(),
                    $agentConfig->getCaptureSessionVariablesWhitelist()
                )
                : null;
            $this->PostDataRaw = $agentConfig->getCaptureRawPostData() ? file_get_contents('php://input'): null;
        } else {
            $this->Headers = $this->getHeaders(null, array('*'));
            $this->Cookies = isset($_COOKIE) ? self::getRequestMap($_COOKIE, array('*'), array('*')) : null;
            $this->QueryString = isset($_GET) ? self::getRequestMap($_GET, null, array('*')) : null;
            $this->PostData = isset($_POST) ? self::getRequestMap($_POST, null, array('*')) : null;
            $this->SessionData = isset($_SESSION) ? self::getRequestMap($_SESSION, array('*'), array('*')) : null;
            $this->PostDataRaw = file_get_contents('php://input');
        }
    }

    /**
     * Singleton attributes
     */
    protected function __clone()
    {
    }

    /**
     * Get singleton instance
     *
     * @return self
     */
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
     *
     * @param mixed   $data      Collection
     * @param boolean $blacklist Blacklist
     * @param boolean $whitelist Whitelist
     *
     * @return array
     */
    public static function getRequestMap($data, $blacklist = null, $whitelist = null)
    {
        if (!is_array($whitelist)) {
            $whitelist = null;
        }

        if (!is_array($blacklist)) {
            $blacklist = null;
        }

        if (empty($whitelist)) {
            return null;
        }

        if (empty($blacklist)) {
            $blacklist = null;
        }

        $whitelistAll = false;
        $blacklistAll = false;

        if ($blacklist) {
            if ((true == isset($blacklist[0]) && $blacklist[0] == '*')
                || true == isset($blacklist['*'])
            ) {
                $blacklistAll = true;
            }
        }

        if ($whitelist) {
            if ((true == isset($whitelist[0]) && $whitelist[0] == '*')
                || true == isset($whitelist['*'])
            ) {
                $whitelistAll = true;
            }
        }

        $result = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $maskValue = false;

                if ($blacklist) {
                    if ($blacklistAll
                        || true == isset($blacklist[$key])
                    ) {
                        $maskValue = true;
                    }
                }
    
                if (!$whitelistAll
                    && false == isset($whitelist[$key])
                ) {
                    continue;
                }

                $result[$key] = $maskValue
                    ? self::HIDDEN_VALUE
                    : TypeConverter::stringify($value);
            }
        }

        return empty($result) ? null : $result;
    }

    /**
     * Get User IP Address
     *
     * @return string
     */
    protected function getIpAddress()
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

    /**
     * Get Protocol
     *
     * @return string
     */
    protected function getProtocol()
    {
        $protocol = null;
        if ('cli' === php_sapi_name()) {
            $protocol = 'CLI';
        } else {
            $protocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');
        }
        return $protocol;
    }

    /**
     * Get Request URL
     *
     * @return string
     */
    protected function getRequestUrl()
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

    /**
     * Get headers
     *
     * @param array $blacklist Blacklist
     * @param array $whitelist Whitelist
     *
     * @return void
     */
    protected function getHeaders($blacklist = null, $whitelist = null)
    {
        $headers = array();
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (false === $headers) {
                // getallheaders() returns false in case of error
                $headers = array();
            }
        }

        return self::getRequestMap($headers, $blacklist, $whitelist);
    }

}