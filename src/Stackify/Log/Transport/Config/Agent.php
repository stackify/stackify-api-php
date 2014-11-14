<?php

namespace Stackify\Log\Transport\Config;

// @TODO set release values
class Agent
{
    const SOCKET_PROTOCOL = 'tcp';
    const SOCKET_HOST = '127.0.0.1';
    const SOCKET_PORT = 1234;
    const SOCKET_TIMEOUT_CONNECT = 2;
    const SOCKET_TIMEOUT_WRITE = 2;
    const SOCKET_MAX_CONNECT_ATTEMPTS = 2;
}