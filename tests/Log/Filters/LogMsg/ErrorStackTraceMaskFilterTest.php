<?php

namespace Stackify\Tests\Log\Entities;

use Stackify\Log\Filters\LogMsg\ErrorStackTraceMaskFilter;
use Stackify\Log\Entities\Api\LogMsg;
use Stackify\Log\Entities\Api\StackifyError;

class ErrorStackTraceMaskFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testMaskNormalYiiStackTrace()
    {
        $filter = new ErrorStackTraceMaskFilter();
        $input = file_get_contents(
            __DIR__.
            DIRECTORY_SEPARATOR.
            'Fixtures'.
            DIRECTORY_SEPARATOR.
            'ErrorStackTraceMaskFilterInput.txt'
        );

        $logMsg = new LogMsg(
            'info',
            $input,
            0
        );

        $logMsg->setError(
            new StackifyError('test', 'test')
        );
        
        $output = file_get_contents(
            __DIR__.
            DIRECTORY_SEPARATOR.
            'Fixtures'.
            DIRECTORY_SEPARATOR.
            'ErrorStackTraceMaskFilterOutput.txt'
        );

        $message = $filter->filter($logMsg);
        $this->assertSame($message->getMessage(), $output);
    }
}
