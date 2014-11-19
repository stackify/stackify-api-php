<?php

class MyException extends \Exception {}
class NewException extends \Exception {}

function test() {
    throw new MyException('a');
}

function trycatch($logger) {
    try {
        $logger->error('no exception');
        test();
    } catch (MyException $e) {
        throw new NewException('catch', 20, $e);
    }
}

function nestedExceptions($logger) {
    $ex = null;
    for ($i = 1; $i <= 10; $i++) {
        try {
            throw new \Exception("Number: $i", 42, $ex);
        } catch (\Exception $ex) {
        }
    }
    $logger->error('ex', [$ex]);
}

nestedExceptions($logger);

trycatch($logger);

