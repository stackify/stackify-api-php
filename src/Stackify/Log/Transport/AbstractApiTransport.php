<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Transport\Config\Api;
use Stackify\Log\Entities\LogEntryInterface;
use Stackify\Exceptions\InitializationException;

abstract class AbstractApiTransport extends AbstractTransport
{

    protected $apiKey;
    protected $proxy;
    protected $queue = array();

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct();
        $this->apiKey = $this->validateApiKey($apiKey);
        $this->extractOptions($options);
        register_shutdown_function(array($this, 'finish'));
    }

    public function addEntry(LogEntryInterface $logEntry)
    {
        $logMsg = $this->messageBuilder->createLogMsg($logEntry);
        if (!$this->errorGovernor->shouldBeSent($logMsg)) {
            $logMsg->Ex = null;
        }
        $this->queue[] = $logMsg;
    }

    public function finish()
    {
        if (!empty($this->queue)) {
            $json = $this->messageBuilder->getApiMessage($this->queue);
            // empty queue to avoid duplicates
            $this->queue = array();
            if ($this->getDebug()) {
                $this->logDebug('['.get_class().'] Request Body: %s', $json);
            }
            $this->send($json);
        }
    }

    protected abstract function send($data);

    protected function getAllowedOptions()
    {
        return array(
            'proxy' => '/.+/',
            'debug' => '/^(0|1)?$/',  // boolean
        );
    }

    protected function getApiHeaders()
    {
        return array(
            'Content-Type' => 'application/json',
            'X-Stackify-PV' => $this->agentConfig ? $this->agentConfig->getApiVersionHeader(): Api::API_VERSION_HEADER,
            'X-Stackify-Key' => $this->apiKey,
        );
    }

    private function validateApiKey($value)
    {
        $apiKey = trim($value);
        if (empty($apiKey)) {
            throw new InitializationException('API key cannot be empty');
        }
        return $apiKey;
    }

}