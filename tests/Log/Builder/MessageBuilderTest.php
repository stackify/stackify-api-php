<?php

namespace Stackify\Tests\Log\Builder;

use Stackify\Log\Builder\MessageBuilder;
use Stackify\Log\Entities\LogEntryInterface;
use Stackify\Log\Standalone\LogEntry;
use Stackify\Log\Transport\Config\Agent;
use Stackify\Tests\Log\Builder\Fixtures\ExampleLogMsgFilter;
use Stackify\Tests\Log\Builder\Fixtures\ExampleLogMsgFilterWithStackTrace;

class MessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testLogMessageFiltersNotExist()
    {
        $agentConfig = new Agent();
        $agentConfig->setLogMessageFilters(
            [
                'NonExistentClass'
            ]
        );

        $messageBuilder = new MessageBuilder(
            'test-logger',
            'app',
            'env',
            false,
            $agentConfig
        );

        $this->assertSame(count($messageBuilder->getLogMessageFilters()), 0);
    }

    public function testLogMessageFiltersExist()
    {
        $agentConfig = new Agent();
        $agentConfig->setLogMessageFilters(
            [
                ExampleLogMsgFilter::class
            ]
        );

        $messageBuilder = new MessageBuilder(
            'test-logger',
            'app',
            'env',
            false,
            $agentConfig
        );

        $this->assertSame(count($messageBuilder->getLogMessageFilters()), 1);
    }

    public function testLogMessageFiltersExistCreateLogMsg()
    {
        $agentConfig = new Agent();
        $agentConfig->setLogMessageFilters(
            [
                ExampleLogMsgFilter::class
            ]
        );

        $messageBuilder = new MessageBuilder(
            'test-logger',
            'app',
            'env',
            false,
            $agentConfig
        );

        $this->assertSame(count($messageBuilder->getLogMessageFilters()), 1);

        $logMsg = $messageBuilder->createLogMsg(
            $this->createLogEntry(
                'error',
                'sample message'
            )
        );
        $this->assertSame($logMsg->getMessage(), 'test filter');
    }

    public function testMaskErrorStackTraceArgumentsCreateLogMsg()
    {
        $agentConfig = new Agent();
        $agentConfig->setMaskErrorStackTraceArguments(true);

        $messageBuilder = new MessageBuilder(
            'test-logger',
            'app',
            'env',
            false,
            $agentConfig
        );

        $this->assertSame(count($messageBuilder->getLogMessageFilters()), 1);
        $logEntry = $this->createLogEntry(
            'error',
            "Stack trace:\n : SomeFunction(\'test\')",
            array(
                'error' => new \Exception('test')
            )
        );

        $logMsg = $messageBuilder->createLogMsg(
            $logEntry
        );

        $this->assertSame($logMsg->getMessage(), "Stack trace:\n : SomeFunction()");
    }

    public function testMaskErrorStackTraceArgumentsWithFiltersCreateLogMsg()
    {
        $agentConfig = new Agent();
        $agentConfig->setMaskErrorStackTraceArguments(true);
        $agentConfig->setLogMessageFilters(
            [
                ExampleLogMsgFilterWithStackTrace::class
            ]
        );

        $messageBuilder = new MessageBuilder(
            'test-logger',
            'app',
            'env',
            false,
            $agentConfig
        );

        $this->assertSame(count($messageBuilder->getLogMessageFilters()), 2);
        $logEntry = $this->createLogEntry(
            'error',
            'Test1',
            array(
                'error' => new \Exception('test')
            )
        );

        $logMsg = $messageBuilder->createLogMsg(
            $logEntry
        );

        $this->assertSame(
            $logMsg->getMessage(),
            "Stack trace:\n : SomeExampleFunctionTest1()" // Test1 just to confirm we filter the first message on the pipeline
        );
    }

    public function testCreateLogMsgWithStackifyError()
    {
        $agentConfig = new Agent();

        $messageBuilder = new MessageBuilder(
            'test-logger',
            'app',
            'env',
            false,
            $agentConfig
        );

        $this->assertSame(count($messageBuilder->getLogMessageFilters()), 0);
        $logEntry = $this->createLogEntry(
            'error',
            'Test1',
            array(
                'error' => new \Exception('test')
            )
        );

        $logMsg = $messageBuilder->createLogMsg(
            $logEntry
        );

        $this->assertSame(
            $logMsg->getMessage(),
            'Test1'
        );
    }


    /**
     * Create log entry sample
     *
     * @param string $level   Log level
     * @param string $message Log message
     * @param array  $context Log context
     *
     * @return LogEntryInterface
     */
    public function createLogEntry($level, $message, array $context = array())
    {
        $logEvent = array(
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'milliseconds' => round(microtime(true) * 1000),
        );

        return new LogEntry($logEvent);
    }
}
