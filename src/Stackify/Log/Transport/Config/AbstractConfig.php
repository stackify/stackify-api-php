<?php
namespace Stackify\Log\Transport\Config;

/**
 * Abstract config class
 */
abstract class AbstractConfig
{
    /**
     * Capture $_SERVER variables
     *
     * @var boolean
     */
    protected $CaptureServerVariables;
    /**
     * Capture $_SERVER variables whitelist
     *
     * @var array
     */
    protected $CaptureServerVariablesWhitelist;
    /**
     * Capture $_SERVER variables blacklist
     *
     * @var array
     */
    protected $CaptureServerVariablesBlacklist;
    /**
     * Capture $_GET variables
     *
     * @var boolean
     */
    protected $CaptureGetVariables;
    /**
     * Capture $_GET variables whitelist
     *
     * @var array
     */
    protected $CaptureGetVariablesWhitelist;
    /**
     * Capture $_GET variables blacklist
     *
     * @var array
     */
    protected $CaptureGetVariablesBlacklist;
    /**
     * Capture $_POST variables
     *
     * @var boolean
     */
    protected $CapturePostVariables;
    /**
     * Capture $_POST variables whitelist
     *
     * @var array
     */
    protected $CapturePostVariablesWhitelist;
    /**
     * Capture $_POST variables blacklist
     *
     * @var array
     */
    protected $CapturePostVariablesBlacklist;

    /**
     * Capture $_SESSION variables
     *
     * @var boolean
     */
    protected $CaptureSessionVariables;
    /**
     * Capture $_SESSION variables whitelist
     *
     * @var array
     */
    protected $CaptureSessionVariablesWhitelist;
    /**
     * Capture $_SESSION variables blacklist
     *
     * @var array
     */
    protected $CaptureSessionVariablesBlacklist;

    /**
     * Capture `getallheaders` variables
     *
     * @var boolean
     */
    protected $CaptureErrorHeaders;
    /**
     * Capture `getallheaders` variables whitelist
     *
     * @var array
     */
    protected $CaptureErrorHeadersWhitelist;
    /**
     * Capture `getallheaders` variables blacklist
     *
     * @var array
     */
    protected $CaptureErrorHeadersBlacklist;
    /**
     * Capture `getallheaders` variables
     *
     * @var boolean
     */
    protected $CaptureErrorCookies;
    /**
     * Capture `getallheaders` variables whitelist
     *
     * @var array
     */
    protected $CaptureErrorCookiesWhitelist;
    /**
     * Capture `getallheaders` variables blacklist
     *
     * @var array
     */
    protected $CaptureErrorCookiesBlacklist;
    /**
     * Capture raw POST data
     *
     * @var boolean
     */
    protected $CaptureRawPostData;
    /**
     * Debug Log Path
     *
     * @var string
     */
    protected $DebugLogPath;
    /**
     * Debug Setting
     *
     * @var boolean
     */
    protected $Debug;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CaptureServerVariables = true;
        $this->CaptureServerVariablesWhitelist = array('*');
        $this->CaptureServerVariablesBlacklist = null;

        $this->CapturePostVariables = false;
        $this->CapturePostVariablesWhitelist = array('*');
        $this->CapturePostVariablesBlacklist = null;

        $this->CaptureGetVariables = true;
        $this->CaptureGetVariablesWhitelist = array('*');
        $this->CaptureGetVariablesBlacklist = null;

        $this->CaptureSessionVariables = true;
        $this->CaptureSessionVariablesWhitelist = array('*');
        $this->CaptureSessionVariablesBlacklist = array('*');

        $this->CaptureErrorHeaders = true;
        $this->CaptureErrorHeadersWhitelist = array('*');
        $this->CaptureErrorHeadersBlacklist = null;

        $this->CaptureErrorCookies = true;
        $this->CaptureErrorCookiesWhitelist = array('*');
        $this->CaptureErrorCookiesBlacklist = array('*');

        $this->CaptureRawPostData = false;

        $ds = DIRECTORY_SEPARATOR;
        $this->DebugLogPath = realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'debug/log.log';
        $this->Debug = false;
    }

    /**
     * Set capture raw POST data option
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setCaptureRawPostData($enable = null)
    {
        $this->CaptureRawPostData = $this->getBoolean($enable, 'CaptureRawPostData');
    }
    /**
     * Set capture $_SERVER variable option
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setCaptureServerVariables($enable = null)
    {
        $this->CaptureServerVariables = $this->getBoolean($enable, 'CaptureServerVariables');
    }
    /**
     * Set capture $_SERVER whitelist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureServerVariablesWhitelist($rawConfig = null)
    {
        $this->CaptureServerVariablesWhitelist = $this->parseStringToArray($rawConfig, 'CaptureServerVariablesWhitelist');
    }
    /**
     * Set capture $_SERVER blacklist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureServerVariablesBlacklist($rawConfig = null)
    {
        $this->CaptureServerVariablesBlacklist = $this->parseStringToArray($rawConfig, 'CaptureServerVariablesBlacklist');
    }
    /**
     * Set capture $_POST variable option
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setCapturePostVariables($enable = null)
    {
        $this->CapturePostVariables = $this->getBoolean($enable, 'CapturePostVariables');
    }
    /**
     * Set capture $_POST whitelist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCapturePostVariablesWhitelist($rawConfig = null)
    {
        $this->CapturePostVariablesWhitelist = $this->parseStringToArray($rawConfig, 'CapturePostVariablesWhitelist');
    }
    /**
     * Set capture $_POST blacklist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCapturePostVariablesBlacklist($rawConfig = null)
    {
        $this->CapturePostVariablesBlacklist = $this->parseStringToArray($rawConfig, 'CapturePostVariablesBlacklist');
    }
    /**
     * Set capture $_GET variable option
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setCaptureGetVariables($enable = null)
    {
        $this->CaptureGetVariables = $this->getBoolean($enable, 'CaptureGetVariables');
    }
    /**
     * Set capture $_GET whitelist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureGetVariablesWhitelist($rawConfig = null)
    {
        $this->CaptureGetVariablesWhitelist = $this->parseStringToArray($rawConfig, 'CaptureGetVariablesWhitelist');
    }
    /**
     * Set capture $_GET blacklist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureGetVariablesBlacklist($rawConfig = null)
    {
        $this->CaptureGetVariablesBlacklist = $this->parseStringToArray($rawConfig, 'CaptureGetVariablesBlacklist');
    }
    /**
     * Set capture $_SESSION variable option
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setCaptureSessionVariables($enable = null)
    {
        $this->CaptureSessionVariables = $this->getBoolean($enable, 'CaptureSessionVariables');
    }
    /**
     * Set capture $_SESSION whitelist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureSessionVariablesWhitelist($rawConfig = null)
    {
        $this->CaptureSessionVariablesWhitelist = $this->parseStringToArray($rawConfig, 'CaptureSessionVariablesWhitelist');
    }
    /**
     * Set capture $_SESSION blacklist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureSessionVariablesBlacklist($rawConfig = null)
    {
        $this->CaptureSessionVariablesBlacklist = $this->parseStringToArray($rawConfig, 'CaptureSessionVariablesBlacklist');
    }
    /**
     * Set capture `getallheaders` variable option
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setCaptureErrorHeaders($enable = null)
    {
        $this->CaptureErrorHeaders = $this->getBoolean($enable, 'CaptureErrorHeaders');
    }
    /**
     * Set capture `getallheaders` whitelist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureErrorHeadersWhitelist($rawConfig = null)
    {
        $this->CaptureErrorHeadersWhitelist = $this->parseStringToArray($rawConfig, 'CaptureErrorHeadersWhitelist');
    }
    /**
     * Set capture `getallheaders` blacklist
     *
     * @param array $rawConfig From config
     *
     * @return void
     */
    public function setCaptureErrorHeadersBlacklist($rawConfig = null)
    {
        $this->CaptureErrorHeadersBlacklist = $this->parseStringToArray($rawConfig, 'CaptureErrorHeadersBlacklist');
    }
    /**
     * Set capture $_COOKIE variable option
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setCaptureErrorCookies($enable = null)
    {
        $this->CaptureErrorCookies = $this->getBoolean($enable, 'CaptureErrorCookies');
    }
    /**
     * Set capture $_COOKIE whitelist
     *
     * @param array $rawConfig From config
     *
     * @return mixed
     */
    public function setCaptureErrorCookiesWhitelist($rawConfig = null)
    {
        $this->CaptureErrorCookiesWhitelist = $this->parseStringToArray($rawConfig, 'CaptureErrorCookiesWhitelist');
    }
    /**
     * Set capture $_COOKIE blacklist
     *
     * @param array $rawConfig From config
     *
     * @return mixed
     */
    public function setCaptureErrorCookiesBlacklist($rawConfig = null)
    {
        $this->CaptureErrorCookiesBlacklist = $this->parseStringToArray($rawConfig, 'CaptureErrorCookiesBlacklist');
    }
    /**
     * Set Debug Log Path
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setDebugLogPath($path)
    {
        if ($path == null) {
            $this->log('[DebugLogPath] is not valid.');
            return;
        }

        $this->DebugLogPath = $path;
    }
    /**
     * Set Debug Settings
     *
     * @param boolean $enable Enable
     *
     * @return void
     */
    public function setDebug($enable = null)
    {
        $this->Debug = $this->getBoolean($enable, 'Debug');
    }


    /**
     * Get capture raw POST data option
     *
     * @return boolean
     */
    public function getCaptureRawPostData()
    {
        return $this->CaptureRawPostData;
    }
    /**
     * Get capture $_SERVER variable option
     *
     * @return boolean
     */
    public function getCaptureServerVariables()
    {
        return $this->CaptureServerVariables;
    }
    /**
     * Get capture $_SERVER whitelist
     *
     * @return mixed
     */
    public function getCaptureServerVariablesWhitelist()
    {
        return $this->CaptureServerVariablesWhitelist;
    }
    /**
     * Get capture $_SERVER blacklist
     *
     * @return mixed
     */
    public function getCaptureServerVariablesBlacklist()
    {
        return $this->CaptureServerVariablesBlacklist;
    }
    /**
     * Get capture $_POST variable option
     *
     * @return boolean
     */
    public function getCapturePostVariables()
    {
        return $this->CapturePostVariables;
    }
    /**
     * Get capture $_POST whitelist
     *
     * @return mixed
     */
    public function getCapturePostVariablesWhitelist()
    {
        return $this->CapturePostVariablesWhitelist;
    }
    /**
     * Get capture $_POST blacklist
     *
     * @return mixed
     */
    public function getCapturePostVariablesBlacklist()
    {
        return $this->CapturePostVariablesBlacklist;
    }
    /**
     * Get capture $_GET variable option
     *
     * @return boolean
     */
    public function getCaptureGetVariables()
    {
        return $this->CaptureGetVariables;
    }
    /**
     * Get capture $_GET whitelist
     *
     * @return mixed
     */
    public function getCaptureGetVariablesWhitelist()
    {
        return $this->CaptureGetVariablesWhitelist;
    }
    /**
     * Get capture $_GET blacklist
     *
     * @return mixed
     */
    public function getCaptureGetVariablesBlacklist()
    {
        return $this->CaptureGetVariablesBlacklist;
    }
    /**
     * Get capture $_SESSION variable option
     *
     * @return boolean
     */
    public function getCaptureSessionVariables()
    {
        return $this->CaptureSessionVariables;
    }
    /**
     * Get capture $_SESSION whitelist
     *
     * @return mixed
     */
    public function getCaptureSessionVariablesWhitelist()
    {
        return $this->CaptureSessionVariablesWhitelist;
    }
    /**
     * Get capture $_SESSION blacklist
     *
     * @return mixed
     */
    public function getCaptureSessionVariablesBlacklist()
    {
        return $this->CaptureSessionVariablesBlacklist;
    }
    /**
     * Get capture `getallheaders` variable option
     *
     * @return boolean
     */
    public function getCaptureErrorHeaders()
    {
        return $this->CaptureErrorHeaders;
    }
    /**
     * Get capture `getallheaders` whitelist
     *
     * @return mixed
     */
    public function getCaptureErrorHeadersWhitelist()
    {
        return $this->CaptureErrorHeadersWhitelist;
    }
    /**
     * Get capture `getallheaders` blacklist
     *
     * @return mixed
     */
    public function getCaptureErrorHeadersBlacklist()
    {
        return $this->CaptureErrorHeadersBlacklist;
    }
    /**
     * Get capture $_COOKIE variable option
     *
     * @return boolean
     */
    public function getCaptureErrorCookies()
    {
        return $this->CaptureErrorCookies;
    }
    /**
     * Get capture $_COOKIE whitelist
     *
     * @return mixed
     */
    public function getCaptureErrorCookiesWhitelist()
    {
        return $this->CaptureErrorCookiesWhitelist;
    }
    /**
     * Get capture $_COOKIE blacklist
     *
     * @return mixed
     */
    public function getCaptureErrorCookiesBlacklist()
    {
        return $this->CaptureErrorCookiesBlacklist;
    }
    /**
     * Get Debug log path
     *
     * @return string
     */
    public function getDebugLogPath()
    {
        return $this->DebugLogPath;
    }
    /**
     * Get Debug setting
     *
     * @return boolean
     */
    public function getDebug()
    {
        return $this->Debug;
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
     * Log debug message
     *
     * @param string $message String
     *
     * @return void
     */
    protected function logDebug($message)
    {
        if (!$this->getDebug()) {
            return;
        }
        $this->log($message, func_get_args(), true);
    }

    /**
     * Log message
     *
     * @param string  $message Message
     * @param mixed   $args    Context
     * @param boolean $success Success
     *
     * @return void
     */
    protected function log($message, $args, $success = true)
    {
        $replacements = array_slice($args, 1);
        $prefix = $success ? 'Stackify Log' : 'Stackify Error';
        $template = "[$prefix][Config] $message";
        $formatted = preg_replace('/\r\n/', '', vsprintf($template, $replacements));
        // first option - write to local file if possible
        // this can be not available because of file permissions
        @file_put_contents($this->getDebugLogPath(), "$formatted\n", FILE_APPEND);
        if (!$success) {
            // second option - send to default PHP error log
            error_log($formatted);
        }
    }

    /**
     * Extract config from array
     *
     * @param string|array $config Config
     *
     * @return void
     */
    public function extract($config = null)
    {
        if (is_array($config) == false) {
            $this->log('['. __CLASS__ .']['. __FUNCTION__ .'] $config is not an array.');
            return;
        }

        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $method = 'set' . $key;

                if (method_exists($this, $method)) {
                    $this->$method($value);
                    continue;
                }

                $this->log('['. __CLASS__ .']['. __FUNCTION__ .']' . $method .' does not exists.');
            }
        }
    }

    /**
     * Parse comma-delimited string to array
     *
     * @param string $string   String
     * @param string $property Property
     *
     * @return void
     */
    protected function parseStringToArray($string = null, $property = null)
    {
        if (empty($string)) {
            return null;
        }

        if (is_string($string) == false && is_array($string) == false) {
            $this->logError('['.$property.'] is not a comma-delimited string or array.');
            return null;;
        }

        $parsedString = $string;
        if (is_string($string)) {
            $parsedString = array_map('trim', explode(',', $string));
        }

        return array_flip($parsedString);
    }
    /**
     * Get boolean
     *
     * @param boolean $enable   Enable
     * @param string  $property Property
     *
     * @return void
     */
    protected function getBoolean($enable = null,  $property = null)
    {
        if (is_bool($enable) == false) {
            $this->log('['.$property.'] is not a boolean.');
            return null;
        }

        return $enable;
    }
}