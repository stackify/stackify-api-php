<?php

namespace Stackify\Log\Log4php;

use Stackify\Log\Builder\MessageBuilder;
use Stackify\Exceptions\InitializationException;

class Appender extends \LoggerAppender
{

    const MODE_AGENT = 'Agent';
    const MODE_CURL = 'Curl';
    const MODE_EXEC = 'Exec';

    /**
     * @var \Stackify\Log\Transport\TransportInterface
     */
    private $transport;
    private $appName;
    private $environmentName;
    private $apiKey;
    private $mode;
    private $proxy;
    private $debug;

    protected $requiresLayout = false;

    public function __construct($name = '')
    {
        parent::__construct($name);
    }

    public function setAppName($appName)
    {
        $this->appName = $this->validateNotEmpty('AppName', $appName);
    }

    public function setEnvironmentName($environmentName)
    {
        $this->environmentName = $environmentName;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $this->validateNotEmpty('ApiKey', $apiKey);
    }

    public function setMode($mode)
    {
        $this->mode = ucfirst(strtolower($mode));
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    protected function append(\LoggerLoggingEvent $event)
    {
        if (null === $this->transport) {
            $messageBuilder = new MessageBuilder('Stackify log4php v.1.0', $this->appName, $this->environmentName);
            $this->transport = $this->createTransport();
            $this->transport->setMessageBuilder($messageBuilder);
        }
        $logEntry = new LogEntry($event);
        $this->transport->addEntry($logEntry);
    }

    public function close()
    {
        parent::close();
        if (null !== $this->transport) {
            $this->transport->finish();
        }
    }

    private function validateNotEmpty($name, $value)
    {
        $result = trim($value);
        if (empty($result)) {
            throw new InitializationException("$name cannot be empty");
        }
        return $result;
    }

    /**
     * @return \Stackify\Log\Transport\TransportInterface
     */
    private function createTransport()
    {
        $options = array(
            'proxy' => $this->proxy,
            'debug' => $this->debug,
        );
        if (null === $this->mode) {
            $this->mode = self::MODE_AGENT;
        }
        $allowed = array(
            self::MODE_AGENT,
            self::MODE_CURL,
            self::MODE_EXEC,
        );
        if (in_array($this->mode, $allowed)) {
            $className = '\Stackify\Log\Transport\\' . $this->mode . 'Transport';
            return new $className($this->apiKey, $options);
        }
        throw new InitializationException("Mode '$this->mode' is not supported");
    }

}