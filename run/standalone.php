<?php

require '../vendor/autoload.php';

//$transport = null;
$transport = new Stackify\Log\Transport\CurlTransport('aa');
$logger = new Stackify\Log\Standalone\Logger('test.com', 'myPC', $transport);

session_start();
$_SESSION['user_id'] = 42;

try {
    include 'exception.php';
} catch (Exception $ex) {
    $logger->warning('test', ['exception' => $ex, 'a' => 'b']);
}

$logger->info('message');
$logger->alert('foo');
