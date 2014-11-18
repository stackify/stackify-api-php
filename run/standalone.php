<?php

require '../vendor/autoload.php';

//$transport = null;
$transport = new Stackify\Log\Transport\CurlTransport('0Zw8Fj4Hr3Aa1Sf2Gw4Cb3Gk7Fp6Zn6Sc0Gw2Cr');
$logger = new Stackify\Log\Standalone\Logger('test.com', 'myPC', $transport);

session_start();
$_SESSION['user_id'] = 42;

try {
    include 'exception.php';
} catch (Exception $ex) {
    $logger->warning('test', ['exception' => $ex, 'a' => 'b']);
}

$logger->info('mess\'age');
$logger->alert('test');
