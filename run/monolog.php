<?php

require '../vendor/autoload.php';


$transport = null;new Stackify\Log\Transport\CurlTransport('0Zw8Fj4Hr3Aa1Sf2Gw4Cb3Gk7Fp6Zn6Sc0Gw2Cr');
$handler = new Stackify\Log\Monolog\Handler('test.com', 'monologPc', $transport);
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