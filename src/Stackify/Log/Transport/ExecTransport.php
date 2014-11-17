<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Transport\Config\Api;

/**
 * @TODO
 */
class ExecTransport extends AbstractApiTransport
{

    private $curlPath = 'curl';

    public function __construct($apiKey, array $options = array())
    {
        parent::__construct($apiKey, $options);
        if (isset($options['curlPath'])) {
            $this->curlPath = $options['curlPath'];
        }
    }

    protected function getAllowedOptions()
    {
        return array(
            'curlPath',
        );
    }

    protected function getTransportName()
    {
        return 'ExecTransport';
    }

    protected function send($data)
    {
        echo $data;
        // @TODO
        $url = Api::API_BASE_URL;
        $cmd = "$this->curlPath -X POST";
        foreach ($this->getApiHeaders() as $name => $value) {
            $cmd .= " -H '$name: $value'";
        }
        // -d = data, -m = max time
        $cmd .= " -d '$data' '$url' -m 5";
        // exec will return immediately while curl will run in the background
        $cmd .= ' > /dev/null 2>&1 &';
        exec($cmd);
    }

}