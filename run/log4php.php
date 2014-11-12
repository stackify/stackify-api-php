<?php

require '../vendor/autoload.php';

Logger::configure('config/log4php.xml');
$logger = Logger::getLogger('logger_name');

session_start();
$_SESSION['user_id'] = 42;

try {
    include 'exception.php';
} catch (Exception $ex) {
    $logger->warn('test', $ex);
}

$logger->debug('foo');
