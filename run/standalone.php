<?php

require '../vendor/autoload.php';

$logger = new Stackify\Log\Standalone\Logger('test.com', 'myPC');

session_start();
$_SESSION['user_id'] = 42;

try {
    include 'exception.php';
} catch (Exception $ex) {
    $logger->warning('test', ['exception' => $ex, 'a' => 'b']);
}

$logger->alert('foo');
