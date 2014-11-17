<?php

require '../vendor/autoload.php';

//$transport = null;
$transport = new Stackify\Log\Transport\ExecTransport('aa', ['debug' => 1, 'proxy' => 'socks5h://5.9.212.53:6441']);
$logger = new Stackify\Log\Standalone\Logger('test.com', 'myPC', $transport);

session_start();
$_SESSION['user_id'] = 42;

try {
    include 'exception.php';
} catch (Exception $ex) {
    $logger->warning('test', ['exception' => $ex, 'a' => 'b']);
}

$logger->info('mess\'age');
$logger->alert('foo');
