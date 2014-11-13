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

trycatch($logger);

