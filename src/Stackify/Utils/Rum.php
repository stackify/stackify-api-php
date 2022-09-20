<?php

namespace Stackify\Utils;

use Stackify\Exceptions\RumValidationException;

/**
 * RUMV2 Support
 */
class Rum
{
    protected $rumScriptUrl;
    protected $rumKey;
    protected $appName;
    protected $environment;
    protected $hasSetup;
    protected $config = array();

    const DEFAULT_RUM_SCRIPT_URL = 'https://stckjs.stackify.com/stckjs.js';
    const DEFAULT_RUM_KEY = '';

    /**
     * This is our container for the one an only instance with get instance
     * Reference: https://refactoring.guru/design-patterns/singleton/php/example
     */
    private static $_instances = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;
        $ds = DIRECTORY_SEPARATOR;
        $this->config['DebugLogPath'] = realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'debug/log.log';
        $this->hasSetup = false;
    }

    /**
     * Get singleton instance
     *
     * @return this
     */
    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(self::$_instances[$cls])) {
            self::$_instances[$cls] = new static();
        }

        return self::$_instances[$cls];
    }

    /**
     * Setup configuration for rum
     *
     * @param string $appName      Application Name
     * @param string $environment  Environment
     * @param string $rumKey       RUM Key
     * @param string $rumScriptUrl RUM Script URL
     *
     * @return this
     */
    public function setupConfiguration($appName, $environment, $rumKey = null, $rumScriptUrl = null, $config = null)
    {
        try {
            $this->appName = $this->validateAppName($appName);
            $this->environment = $this->validateEnvironment($environment);
            $this->rumScriptUrl = $this->checkRumScriptUrl($rumScriptUrl);
            $this->rumKey = $this->checkRumKey($rumKey);
            $this->hasSetup = true;
        } catch (\Exception $e) {
            $this->logError('Unable to setup RUM Configuration. Something went wrong. Message: %s', $e->getMessage());
            // Reset state
            $this->appName = null;
            $this->environment = null;
            $this->rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;
            $this->rumKey = null;
        }

        return $this;
    }

    /**
     * Check RUM Script URL
     *
     * @param string $rumScriptUrl RUM Script URL
     *
     * @return string
     */
    public function checkRumScriptUrl($rumScriptUrl)
    {
        $url = isset($_SERVER['RETRACE_RUM_SCRIPT_URL']) ? $_SERVER['RETRACE_RUM_SCRIPT_URL'] : null;
        if (!$url) {
            $url = $rumScriptUrl;
        }

        return $this->validateRumScriptUrl($url);
    }

    /**
     * Check RUM Key
     *
     * @param string $rumKey RUM Key
     *
     * @return string
     */
    public function checkRumKey($rumKey)
    {
        $key = isset($_SERVER['RETRACE_RUM_KEY']) ? $_SERVER['RETRACE_RUM_KEY'] : null;
        if (!$key) {
            $key = $rumKey;
        }

        return $this->validateRumKey($key);
    }

    /**
     * Validate RUM Script URL
     *
     * @param string $rumScriptUrl RUM Script URL
     *
     * @return void
     */
    public function validateRumScriptUrl($rumScriptUrl)
    {
        if (empty($rumScriptUrl)) {
            return self::DEFAULT_RUM_SCRIPT_URL;
        }

        if (!preg_match('/^((((https?|ftps?|gopher|telnet|nntp):\/\/)|(mailto:|news:))(%[0-9A-Fa-f]{2}|[-()_.!~*\';\/\?:@&=+$,A-Za-z0-9])+)([).!\';\/\?:,][\[:blank:|:blank:\]])?$/', $rumScriptUrl)) {
            throw new RumValidationException('RUM Script URL is in invalid format.');
        }

        return $rumScriptUrl;
    }

    /**
     * Validate RUM Key
     *
     * @param string $rumKey RUM key
     *
     * @return void
     */
    public function validateRumKey($rumKey)
    {
        if (empty($rumKey)) {
            throw new RumValidationException('RUM Key is empty.');
        }

        if (!preg_match('/^[A-Za-z0-9_-]+$/', $rumKey)) {
            throw new RumValidationException('RUM Key is in invalid format.');
        }

        return $rumKey;
    }

    /**
     * Validate Application Name
     *
     * @param string $appName
     *
     * @return string
     */
    public function validateAppName($appName)
    {
        if (empty($appName)) {
            throw new RumValidationException('Application Name is empty.');
        }

        return $appName;
    }

    /**
     * Validate Environment
     *
     * @param string $environment
     *
     * @return string
     */
    public function validateEnvironment($environment)
    {
        if (empty($environment)) {
            throw new RumValidationException('Environment is empty.');
        }

        return $environment;
    }

    /**
     * Get insert rum script tag
     *
     * @return string|null
     */
    public function insertRumScript()
    {
        try {
            if ($this->isProfilerActive()) {
                return $this->getProfilerInsertRumScript();
            }

            if (empty($this->getRumScriptUrl()) || empty($this->getRumKey())) {
                return null;
            }

            $transactionId = $this->getTransactionId();
            $reportingUrl = $this->getReportingUrl();
            $applicationName = $this->getApplicationName();
            $environment = $this->getEnvironment();

            if (empty($transactionId) || empty($reportingUrl) || empty($applicationName) || empty($environment)) {
                return null;
            }

            $rumSettings = array(
                'ID' => $transactionId,
                'Name' => base64_encode(utf8_encode($applicationName)),
                'Env' => base64_encode(utf8_encode($environment)),
                'Trans' => base64_encode(utf8_encode($reportingUrl))
            );

            return '<script type="text/javascript">(window.StackifySettings || (window.StackifySettings = '.json_encode($rumSettings).'))</script><script src="'.$this->getRumScriptUrl().'" data-key="'.$this->getRumKey().'" async></script>';
        } catch (\Exception $e) {
            $this->logError('Unable to insert RUM Script. Something went wrong. Message: %s', $e->getMessage());
        }
    }

    /**
     * Get transaction ID
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return null;
    }

    /**
     * Get reporting URL
     *
     * @return string|null
     */
    public function getReportingUrl()
    {
        if ($this->getSapiName() == 'cli') {
            $phpSelf = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF']: null;
            if ($phpSelf) {
                return basename($phpSelf, '.php');
            }
            return null;
        }

        $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD']: null;
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']: null;
        $reportingUrl = '/';

        if ($requestUri && is_string($requestUri)) {
            $reportingUrl = array_filter(explode('?', $requestUri))[0];
            $reportingUrl = rtrim($reportingUrl, '/');
        }

        return ($requestMethod ? $requestMethod . '-' : '') . $reportingUrl;
    }

    /**
     * Get server api name
     *
     * @return string
     */
    public function getSapiName()
    {
        return php_sapi_name();
    }

    /**
     * Checks if the profiler is active
     *
     * @return boolean
     */
    protected function isProfilerActive()
    {
        return function_exists('stackify_transaction_id') && class_exists($this->getProfilerClass());
    }

    /**
     * Get profiler insert rum script
     *
     * @return string
     */
    public function getProfilerInsertRumScript()
    {
        if ($this->isProfilerActive() && method_exists($this->getProfilerClass(), 'insertRumScript')) {
            $profilerClass = $this->getProfilerClass();
            return $profilerClass::insertRumScript();
        }
        
        return null;
    }

    /**
     * Get profiler class
     *
     * @return string
     */
    public function getProfilerClass()
    {
        return 'Stackify\Profiler';
    }

    /**
     * Get RUM Script URL
     *
     * @return string
     */
    public function getRumScriptUrl()
    {
        return $this->rumScriptUrl;
    }

    /**
     * Check if RUM Util is setup
     *
     * @return boolean
     */
    public function isSetup()
    {
        return $this->hasSetup;
    }

    /**
     * Get debug log path
     *
     * @return string
     */
    public function getDebugLogPath()
    {
        return $this->config && isset($this->config['DebugLogPath']) ? $this->config['DebugLogPath'] : null;
    }

    /**
     * Get Application Name
     *
     * @return string
     */
    public function getApplicationName()
    {
        return $this->appName;
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Get RUM Key
     *
     * @return string
     */
    public function getRumKey()
    {
        return $this->rumKey;
    }

    /**
     * Log error message
     *
     * @param string $message Message
     *
     * @return void
     */
    protected function logError($message)
    {
        $this->log($message, func_get_args(), false);
    }

    /**
     * Log error messages
     *
     * @param string $message
     * @param array $args
     * @param boolean $success
     *
     * @return void
     */
    protected function log($message, $args, $success = true)
    {
        $replacements = array_slice($args, 1);
        $prefix = $success ? 'Stackify Log' : 'Stackify Error';
        $template = "[$prefix][RUM] $message";
        $formatted = preg_replace('/\r\n/', '', vsprintf($template, $replacements));
        if ($this->getDebugLogPath()) {
            $this->writeToFile($this->getDebugLogPath(), $formatted);
        }

        if (!$success) {
            $this->writeToErrorLog($formatted);
        }
    }

    /**
     * Write message to file
     *
     * @param string $path
     * @param string $string
     *
     * @return void
     */
    protected function writeToFile($path, $string)
    {
        @file_put_contents($path, "$string\n", FILE_APPEND);
    }

    /**
     * Write message to php built in error log
     *
     * @param string $string
     *
     * @return void
     */
    protected function writeToErrorLog($string)
    {
        error_log($string);
    }
}