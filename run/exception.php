<?php

class MyException extends \Exception {}
class NewException extends \Exception {}

function test() {
    throw new MyException('a');
}

function trycatch() {
    try {
        test();
    } catch (MyException $e) {
        throw new NewException('catch', 20, $e);
    }
}

trycatch();