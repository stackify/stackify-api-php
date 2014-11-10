<?php

require '../vendor/autoload.php';

$logger = new Stackify\Log\Standalone\Logger();

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
    $logger->warning('test', ['exception' => $ex, 'a' => 'b']);
}
$logger->alert('foo');
