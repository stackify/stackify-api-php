<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Transport\Config\Api;
use Stackify\Log\Entities\LogEntryInterface;
use Stackify\Exceptions\InitializationException;

abstract class AbstractApiTransport extends AbstractTransport
{

    private $queue = array();
    private $commonOptions = array(
        'proxy' => '/.+/',
        'debug' => '/^(0|1)?$/',  // boolean
    );
    protected $apiKey;
    protected $proxy;
    protected $debug = false;

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct();
        $this->apiKey = $apiKey;
        $this->extractOptions($options);
        register_shutdown_function(array($this, 'finish'));
    }

    public function addEntry(LogEntryInterface $logEntry)
    {
        $this->queue[] = $this->messageBuilder->createLogMsg($logEntry);
    }

    public function finish()
    {
        if (!empty($this->queue)) {
            $json = $this->messageBuilder->getApiMessage($this->queue);
            // empty queue to avoid duplicates
            $this->queue = array();
            $this->send($json);
        }
    }

    protected abstract function send($data);

    protected abstract function getAllowedOptions();

    protected function extractOptions($options)
    {
        $allowed = array_merge($this->commonOptions, $this->getAllowedOptions());
        foreach ($allowed as $name => $regex) {
            if (isset($options[$name])) {
                $value = $options[$name];
                if (preg_match($regex, $value)) {
                    $this->$name = $value;
                } else {
                    throw new InitializationException("Option '$name' has invalid value");
                }
            }
        }
    }

    protected function getApiHeaders()
    {
        return array(
            'Content-Type' => 'application/json',
            'X-Stackify-PV' => Api::API_VERSION_HEADER,
            'X-Stackify-Key' => $this->apiKey,
        );
    }

}