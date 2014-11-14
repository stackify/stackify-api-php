<?php

namespace Stackify\Log\Transport;

use Stackify\Log\Entities\LogEntryInterface;
use Stackify\Log\Transport\Config\Agent as Config;

/**
 * Default transport requires Stackify agent installed and running
 * https://stackify.screenstepslive.com/s/3095/m/7787/l/119709-installation-for-linux
 * Transport creates local TCP connection to agent and writes data.
 * Agent aggregates log entries and sends to API.
 */
final class DefaultTransport extends AbstractTransport
{

    private $connectAttempts = 0;
    private $connected = false;
    private $socket;

    const ERROR_CONNECT = 'Cannot connect socket on %s. Is Stackify agent installed and running? [Error code: %d] [Error message: %s]';
    const ERROR_WRITE = 'Cannot write to socket. Is Stackify agent installed and running?';
    const ERROR_CLOSE = 'Cannot close opened socket';

    public function addEntry(LogEntryInterface $logEntry)
    {
        $data = $this->messageBuilder->getAgentMessage($logEntry);
        $this->send($data);
    }

    public function finish()
    {
        // agent trasport does not use queues
        if ($this->connected) {
            if (false === @fclose($this->socket)) {
                $this->logError(self::ERROR_CLOSE);
            }
        }
    }

    protected function getTransportName()
    {
        return 'DefaultTransport';
    }

    private function send($data)
    {
        $this->connect();
        if ($this->connected) {
            if (false === @fwrite($this->socket, $data)) {
                $this->logError(self::ERROR_WRITE);
            }
        }
    }

    private function connect()
    {
        while (!$this->connected && $this->connectAttempts < Config::SOCKET_MAX_CONNECT_ATTEMPTS) {
            $this->connectAttempts++;
            $remote = sprintf('%s://%s:%d', Config::SOCKET_PROTOCOL, Config::SOCKET_HOST, Config::SOCKET_PORT);
            $this->socket = @stream_socket_client($remote, $errno, $errstr, Config::SOCKET_TIMEOUT_CONNECT);
            $this->connected = false !== $this->socket;
            if ($this->connected) {
                stream_set_timeout($this->socket, Config::SOCKET_TIMEOUT_WRITE);
            } else {
                $this->logError(self::ERROR_CONNECT, $remote, $errno, $errstr);
            }
        }
    }

}