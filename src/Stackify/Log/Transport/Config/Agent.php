<?php

namespace Stackify\Log\Transport\Config;

class Agent
{
    const SOCKET_PROTOCOL = 'tcp';
    const SOCKET_HOST = '127.0.0.1';
    const SOCKET_PORT = 10515;
    const SOCKET_TIMEOUT_CONNECT = 1;
    const SOCKET_TIMEOUT_WRITE = 1;
    const SOCKET_MAX_CONNECT_ATTEMPTS = 2;
}