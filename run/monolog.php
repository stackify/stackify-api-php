<?php

require '../vendor/autoload.php';

$handler = new Stackify\Log\Monolog\Handler('test.com', 'myPC');

$logger = new Monolog\Logger('test_channel');
$logger->pushHandler($handler);

session_start();
$_SESSION['user_id'] = 42;

try {
    include 'exception.php';
} catch (Exception $ex) {
    $logger->warn('test', ['exception' => $ex, 'a' => 'b']);
}

$errorHandler = new Monolog\ErrorHandler($logger);
$errorHandler->registerErrorHandler([], false);
echo [0,1][2];