<?php

require '../vendor/autoload.php';

$handler = new Monolog\Handler\StreamHandler('php://output');
$handler->setFormatter(new Stackify\Log\Monolog\Formatter());

$logger = new Monolog\Logger('test_channel');
$logger->pushHandler($handler);

session_start();
$_SESSION['user_id'] = 42;
$_ENV['a'] = array('a');
$_ENV['b'] = null;
$_ENV['c'] = 123;
$_ENV['d'] = function() {};
$_ENV['e'] = fopen('D:/work/Stackify/make.sh', 'r');

try {
    include 'exception.php';
} catch (Exception $ex) {
    $logger->warn('test', ['exception' => $ex, 'a' => 'b']);
}

$errorHandler = new Monolog\ErrorHandler($logger);
$errorHandler->registerErrorHandler([], false);
echo [0,1][2];