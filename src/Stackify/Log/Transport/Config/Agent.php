<?php
namespace Stackify\Log\Transport\Config;

/**
 * Agent Config Class
 */
class Agent extends AbstractConfig
{
    const SOCKET_PROTOCOL = 'tcp';
    const SOCKET_HOST = '127.0.0.1';
    const SOCKET_PORT = 10601;
    const SOCKET_TIMEOUT_CONNECT = 1;
    const SOCKET_TIMEOUT_WRITE = 1;
    const SOCKET_MAX_CONNECT_ATTEMPTS = 2;
    const DOMAIN_SOCKET_PATH = '/usr/local/stackify/stackify.sock';

    /**
     * Socket protocol
     *
     * @var integer
     */
    protected $Protocol;
    /**
     * Socket hostname
     *
     * @var string
     */
    protected $Host;
    /**
     * Socket port
     *
     * @var integer
     */
    protected $Port;
    /**
     * Socket connect timeout
     *
     * @var integer
     */
    protected $SocketTimeoutConnect;
    /**
     * Socket write timeout
     *
     * @var integer
     */
    protected $SocketTimeoutWrite;
    /**
     * Socket connect max attempts
     *
     * @var integer
     */
    protected $SocketMaxConnectAttempts;
    /**
     * Socket domain path
     *
     * @var string
     */
    protected $DomainSocketPath;
    /**
     * Api Base Url
     *
     * @var string
     */
    protected $ApiBaseUrl;
    /**
     * Api Url Call Logs Endpoint
     *
     * @var string
     */
    protected $ApiCallLogsEndpoint;
    /**
     * Api Max Time out
     *
     * @var string
     */
    protected $ApiMaxTimeout;
    /**
     * Api Version Header
     *
     * @var string
     */
    protected $ApiVersionHeader;

    protected $ValidProtocols = array('tcp', 'udp');

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Protocol = static::SOCKET_PROTOCOL;
        $this->Host = static::SOCKET_HOST;
        $this->Port = static::SOCKET_PORT;
        $this->SocketTimeoutConnect = static::SOCKET_TIMEOUT_CONNECT;
        $this->SocketTimeoutWrite = static::SOCKET_TIMEOUT_WRITE;
        $this->SocketMaxConnectAttempts = static::SOCKET_MAX_CONNECT_ATTEMPTS;
        $this->DomainSocketPath = static::DOMAIN_SOCKET_PATH;
        $this->ApiBaseUrl = Api::API_BASE_URL;
        $this->ApiCallLogsEndpoint = Api::API_CALL_LOGS;
        $this->ApiMaxTimeout = Api::API_MAX_TIME;
        $this->ApiVersionHeader = Api::API_VERSION_HEADER;

        parent::__construct();
    }

    /**
     * Set protocol for the agent transport
     *
     * @param string $protocol Protocol for the transport
     *
     * @return void
     */
    public function setProtocol($protocol = null)
    {
        if (in_array($protocol, $this->ValidProtocols) == false) {
            $this->logError('[Protocol] is not valid.');
            return;
        }

        $this->Protocol = $protocol;
    }

    /**
     * Set hostname for the agent transport
     *
     * @param string $host Hostname
     *
     * @return void
     */
    public function setHost($host = null)
    {
        if ($host == null) {
            $this->logError('[Host] is not valid.');
        }

        // TODO: Hostname checking
        $this->Host = $host;
    }

    /**
     * Set port for the agent transport
     *
     * @param integer $port Port
     *
     * @return void
     */
    public function setPort($port = null)
    {
        if (is_numeric($port) == false) {
            $this->logError('[Port] is not an integer.');
            return;
        }

        if ($port < 0 && $port > 65535) {
            $this->logError('[Port] is not valid.');
            return;
        }

        $this->Port = $port;
    }

    /**
     * Set socket connection timeout
     *
     * @param integer $timeout Timeout
     *
     * @return void
     */
    public function setSocketTimeoutConnect($timeout = null)
    {
        if (is_numeric($timeout) == false) {
            $this->logError('[SocketTimeoutConnect] is not an integer.');
            return;
        }

        $this->SocketTimeoutConnect = $timeout;
    }

    /**
     * Set socket write timeout
     *
     * @param integer $timeout Timeout
     *
     * @return void
     */
    public function setSocketTimeoutWrite($timeout )
    {
        if (is_numeric($timeout) == false) {
            $this->logError('[SocketTimeoutWrite] is not an integer.');
            return;
        }

        $this->SocketTimeoutWrite = $timeout;
    }

    /**
     * Set socket max connection attempts
     *
     * @param integer $attempts Attempts
     *
     * @return void
     */
    public function setSocketMaxConnectAttempts($attempts)
    {
        if (is_numeric($attempts) == false) {
            $this->logError('[SocketMaxConnectAttempts] is not an integer.');
            return;
        }

        $this->SocketMaxConnectAttempts = $attempts;
    }

    /**
     * Set domain socket path
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setDomainSocketPath($path)
    {
        if ($path == null) {
            $this->logError('[DomainSocketPath] is not valid.');
            return;
        }

        $this->DomainSocketPath = $path;
    }

    /**
     * Set Api Base Url
     *
     * @param string $url URL
     *
     * @return void
     */
    public function setApiBaseUrl($url)
    {
        if ($url == null) {
            $this->logError('[ApiBaseUrl] is not valid.');
            return;
        }

        $this->ApiBaseUrl = $url;
    }
    /**
     * Set Api Call Logs Endpoint
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setApiCallLogsEndpoint($path)
    {
        if ($path == null) {
            $this->logError('[ApiCallLogsEndpoint] is not valid.');
            return;
        }

        $this->ApiCallLogsEndpoint = $path;
    }
    /**
     * Set Api Max Time Out
     *
     * @param integer $timeout Timeout
     *
     * @return void
     */
    public function setApiMaxTimeout($timeout = null)
    {
        if (is_numeric($timeout) == false) {
            $this->logError('[ApiMaxTimeout] is not an integer.');
            return;
        }

        $this->ApiMaxTimeout = $timeout;
    }
    /**
     * Set Api Version Header
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setApiVersionHeader($path)
    {
        if ($path == null) {
            $this->logError('[ApiVersionHeader] is not valid.');
            return;
        }

        $this->ApiVersionHeader = $path;
    }

    /**
     * Get socket protocol
     *
     * @return integer
     */
    public function getProtocol()
    {
        return $this->Protocol;
    }

    /**
     * Get socket host
     *
     * @return integer
     */
    public function getHost()
    {
        return $this->Host;
    }

    /**
     * Get socket port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->Port;
    }
    
    /**
     * Get socket connection timeout
     *
     * @return integer
     */
    public function getSocketTimeoutConnect()
    {
        return $this->SocketTimeoutConnect;
    }

    /**
     * Get socket write timeout
     *
     * @return integer
     */
    public function getSocketTimeoutWrite()
    {
        return $this->SocketTimeoutWrite;
    }

    /**
     * Get socket max connection attempts
     *
     * @return integer
     */
    public function getSocketMaxConnectAttempts()
    {
        return $this->SocketMaxConnectAttempts;
    }

    /**
     * Get socket domain path
     *
     * @return string
     */
    public function getDomainSocketPath()
    {
        return $this->DomainSocketPath;
    }

    /**
     * Get api base url
     *
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->ApiBaseUrl;
    }
    /**
     * Get api call logs endpoint
     *
     * @return string
     */
    public function getApiCallLogsEndpoint()
    {
        return $this->ApiCallLogsEndpoint;
    }
    /**
     * Get api max timeout
     *
     * @return integer
     */
    public function getApiMaxTimeout()
    {
        return $this->ApiMaxTimeout;
    }
    /**
     * Get api version header
     *
     * @return string
     */
    public function getApiVersionHeader()
    {
        return $this->ApiVersionHeader;
    }

    /**
     * Log message
     *
     * @param string  $message Message
     * @param mixed   $args    Log context
     * @param boolean $success Success
     *
     * @return void
     */
    protected function log($message, $args, $success = true)
    {
        return parent::log('['.get_class().']'.$message, $args, $success);
    }

    
    /**
     * Singleton attributes
     * 
     * @return void
     */
    private function __clone()
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
}
