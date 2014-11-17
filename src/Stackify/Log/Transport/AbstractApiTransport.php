<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Entities\LogEntryInterface;

abstract class AbstractApiTransport extends AbstractTransport
{

    private $queue = array();
    private $commonOptions = array('proxy');
    protected $apiKey;
    protected $proxy;

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct();
        $this->apiKey = $apiKey;
        // @TODO validate & merge
        $this->extractOption($options, 'proxy');
        // type, host, port, user, pass
        // @TODO shutdown case
        // register_shutdown_function(array($this, 'finish'));
    }

    public function addEntry(LogEntryInterface $logEntry)
    {
        $this->queue[] = $this->messageBuilder->createLogMsg($logEntry);
    }

    public function finish()
    {
        if (!empty($this->queue)) {
            $json = $this->messageBuilder->getApiMessage($this->queue);
            $this->send($json);
        }
    }

    protected abstract function send($data);

    protected abstract function getAllowedOptions();

    protected function extractOption($options, $optionName)
    {
        $allowed = array_merge($this->commonOptions, $this->getAllowedOptions());
        if (isset($options[$optionName]) && in_array($optionName, $allowed)) {
            $this->$optionName = $options[$optionName];
        }
    }

    protected function getApiHeaders()
    {
        return array(
            'X-Stackify-PV' => 'V1',
            'X-Stackify-Key' => $this->apiKey,
        );
    }

}