<?php

namespace Stackify\Tests\Log\Entities;

use Stackify\Log\Entities\ErrorWrapper;
use Stackify\Log\Transport\Config\Agent;
use Stackify\Tests\Log\Entities\Fixtures\TestException;

class ErrorWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testCaptureExceptionBlacklistWithCustomException()
    {
        $agentConfig = new Agent();
        $agentConfig->setCaptureExceptionClassBlacklist([
            TestException::class
        ]);

        $message = "Test exception random";
        $exception = new TestException($message);
        $errorWrapperObject = new ErrorWrapper($exception, 1, $agentConfig);

        $this->assertSame($message, $errorWrapperObject->getType());
    }

    public function testEmptyCaptureExceptionBlacklistWithCustomException()
    {
        $message = "Test exception random";
        $exception = new TestException($message);
        $errorWrapperObject = new ErrorWrapper($exception);

        $this->assertSame(TestException::class, $errorWrapperObject->getType());
    }
}
