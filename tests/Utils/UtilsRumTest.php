<?php

namespace Stackify\Tests\Utils;

use Stackify\Utils\Rum;
use Stackify\Exceptions\RumValidationException;

class UtilsRumTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_RUM_SCRIPT_URL = 'https://stckjs.stackify.com/stckjs.js';

    public function testRumDefaultSetting()
    {
        $rumObject = $this->givenARumObjectWithDefaultSetting();
        $ds = DIRECTORY_SEPARATOR;

        $this->assertSame($rumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($rumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($rumObject->isSetup(), false);
    }

    public function testRumDefaultSetupConfiguration()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $rumObject = $this->givenARumObjectWithDefaultSetting()
            ->setupConfiguration(
                $appName,
                $environment,
                $rumKey
            );

        $this->assertSame($rumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($rumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($rumObject->isSetup(), true);
        $this->assertSame($rumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($rumObject->getApplicationName(), 'test app name');
        $this->assertSame($rumObject->getEnvironment(), 'test environment');
    }

    public function testRumInsertScriptWithSetupTransactionIdAndReportingUrl()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getTransactionId', 'getReportingUrl'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('getTransactionId')
            ->willReturn($transactionId);

        $mockRumObject->method('getReportingUrl')
            ->willReturn($reportingUrl);

        $rumSettings = array(
            'ID' => $transactionId,
            'Name' => base64_encode(utf8_encode($appName)),
            'Env' => base64_encode(utf8_encode($environment)),
            'Trans' => base64_encode(utf8_encode($reportingUrl))
        );

        $rumScript = '<script type="text/javascript">(window.StackifySettings || (window.StackifySettings = '.json_encode($rumSettings).'))</script><script src="'.$rumScriptUrl.'" data-key="'.$rumKey.'" async></script>';

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($mockRumObject->getApplicationName(), 'test app name');
        $this->assertSame($mockRumObject->getEnvironment(), 'test environment');
        $this->assertSame($mockRumObject->insertRumScript(), $rumScript);
    }

    public function testRumInsertScriptWithProfilerActive()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['isProfilerActive', 'getProfilerInsertRumScript'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('isProfilerActive')
            ->willReturn(true);

        $mockRumObject->method('getProfilerInsertRumScript')
            ->willReturn('mock-script');

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($mockRumObject->getApplicationName(), 'test app name');
        $this->assertSame($mockRumObject->getEnvironment(), 'test environment');
        $this->assertSame($mockRumObject->insertRumScript(), 'mock-script');
    }

    public function testRumInsertScriptWithNoTransactionId()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getReportingUrl'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = '';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('getReportingUrl')
            ->willReturn($reportingUrl);

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($mockRumObject->getApplicationName(), 'test app name');
        $this->assertSame($mockRumObject->getEnvironment(), 'test environment');
        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithNoReportingUrl()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getReportingUrl', 'getTransactionId'])
            ->getMock();

        $reportingUrl = '';
        $transactionId = 'test-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('getTransactionId')
            ->willReturn($transactionId);

        $mockRumObject->method('getReportingUrl')
            ->willReturn($reportingUrl);

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($mockRumObject->getApplicationName(), 'test app name');
        $this->assertSame($mockRumObject->getEnvironment(), 'test environment');
        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithNoAppName()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getReportingUrl', 'getTransactionId', 'getApplicationName'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('getReportingUrl')
            ->willReturn($reportingUrl);

        $mockRumObject->method('getTransactionId')
            ->willReturn($transactionId);

        $mockRumObject->method('getApplicationName')
            ->willReturn('');

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($mockRumObject->getApplicationName(), '');
        $this->assertSame($mockRumObject->getEnvironment(), 'test environment');
        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithNoEnvironment()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getReportingUrl', 'getTransactionId', 'getEnvironment'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('getReportingUrl')
            ->willReturn($reportingUrl);

        $mockRumObject->method('getTransactionId')
            ->willReturn($transactionId);

        $mockRumObject->method('getEnvironment')
            ->willReturn('');

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($mockRumObject->getApplicationName(), 'test app name');
        $this->assertSame($mockRumObject->getEnvironment(), '');
        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithNoRumKey()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getReportingUrl', 'getTransactionId', 'getRumKey'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('getReportingUrl')
            ->willReturn($reportingUrl);

        $mockRumObject->method('getTransactionId')
            ->willReturn($transactionId);

        $mockRumObject->method('getRumKey')
            ->willReturn('');

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), '');
        $this->assertSame($mockRumObject->getApplicationName(), 'test app name');
        $this->assertSame($mockRumObject->getEnvironment(), 'test environment');
        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithNoRumScriptUrl()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getReportingUrl', 'getTransactionId', 'getRumScriptUrl'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('getReportingUrl')
            ->willReturn($reportingUrl);

        $mockRumObject->method('getTransactionId')
            ->willReturn($transactionId);

        $mockRumObject->method('getRumScriptUrl')
            ->willReturn('');

        $this->assertSame($mockRumObject->getRumScriptUrl(), '');
        $this->assertSame($mockRumObject->getDebugLogPath(), realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log');
        $this->assertSame($mockRumObject->isSetup(), true);
        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key');
        $this->assertSame($mockRumObject->getApplicationName(), 'test app name');
        $this->assertSame($mockRumObject->getEnvironment(), 'test environment');
        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithExceptionAndCallLogError()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['isProfilerActive', 'logError'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('isProfilerActive')
            ->will($this->throwException(new \Exception('Dummy Exception')));

        $mockRumObject->expects($this->once())
            ->method('logError')
            ->with(
                'Unable to insert RUM Script. Something went wrong. Message: %s',
                'Dummy Exception'
            );

        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithExceptionAndCallLog()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['isProfilerActive', 'log'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('isProfilerActive')
            ->will($this->throwException(new \Exception('Dummy Exception')));

        $mockRumObject->expects($this->once())
            ->method('log')
            ->with(
                'Unable to insert RUM Script. Something went wrong. Message: %s',
                array(
                    'Unable to insert RUM Script. Something went wrong. Message: %s',
                    'Dummy Exception'
                ),
                false
            );

        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumInsertScriptWithExceptionAndCallWriteToFileAndErrorLog()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['isProfilerActive', 'writeToFile', 'writeToErrorLog'])
            ->getMock();

        $reportingUrl = 'test-1234';
        $transactionId = 'trans-1234';
        $rumScriptUrl = self::DEFAULT_RUM_SCRIPT_URL;

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $mockRumObject->method('isProfilerActive')
            ->will($this->throwException(new \Exception('Dummy Exception')));

        $mockRumObject->expects($this->once())
            ->method('writeToFile')
            ->with(
                realpath(dirname(__FILE__) . "$ds..$ds..") . $ds . 'src/debug/log.log',
                '[Stackify Error][RUM] Unable to insert RUM Script. Something went wrong. Message: Dummy Exception'
            );

        $mockRumObject->expects($this->once())
            ->method('writeToErrorLog')
            ->with(
                '[Stackify Error][RUM] Unable to insert RUM Script. Something went wrong. Message: Dummy Exception'
            );
        $this->assertSame($mockRumObject->insertRumScript(), null);
    }

    public function testRumSetupConfigurationWithEmptyAppName()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('Application Name is empty.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = '';
        $environment = 'test environment';
        $rumKey = '`invalid-rum-key';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );
    }

    public function testRumSetupConfigurationWithEmptyEnvironment()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('Environment is empty.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = '';
        $rumKey = '`invalid-rum-key';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );
    }

    public function testRumSetupConfigurationWithInvalidRumKey()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('RUM Key is in invalid format.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = '`invalid-rum-key';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );
    }

    public function testRumSetupConfigurationWithEmptyRumKey()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('RUM Key is empty.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = '';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );
    }

    public function testRumSetupConfigurationWithInvalidRumScriptUrl()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('RUM Script URL is in invalid format.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $rumScriptUrl = 'invalid-rum-script-url';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey,
            $rumScriptUrl
        );
    }

    public function testRumSetupConfigurationWithEmptyRumScriptUrl()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $rumScriptUrl = '';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey,
            $rumScriptUrl
        );

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);
    }

    public function testRumSetupConfigurationWithValidRumKeyFromEnv()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $_SERVER['RETRACE_RUM_KEY'] = 'valid-rum-key-from-env';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment
        );

        $this->assertSame($mockRumObject->getRumKey(), 'valid-rum-key-from-env');
        unset($_SERVER['RETRACE_RUM_KEY']);
    }

    public function testRumSetupConfigurationWithInvalidRumKeyFromEnv()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('RUM Key is in invalid format.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $_SERVER['RETRACE_RUM_KEY'] = '`invalid-rum-key';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment
        );

        unset($_SERVER['RETRACE_RUM_KEY']);
    }

    public function testRumSetupConfigurationWithEmptyRumKeyFromEnv()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('RUM Key is empty.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $_SERVER['RETRACE_RUM_KEY'] = '';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment
        );

        unset($_SERVER['RETRACE_RUM_KEY']);
    }

    public function testRumSetupConfigurationWithValidRumScriptUrlFromEnv()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $_SERVER['RETRACE_RUM_SCRIPT_URL'] = 'https://test.com/test.js';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $this->assertSame($mockRumObject->getRumScriptUrl(), 'https://test.com/test.js');

        unset($_SERVER['RETRACE_RUM_SCRIPT_URL']);
    }

    public function testRumSetupConfigurationWithInvalidRumScriptUrlFromEnv()
    {
        $this->expectException(RumValidationException::class);
        $this->expectExceptionMessage('RUM Script URL is in invalid format.');

        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $_SERVER['RETRACE_RUM_SCRIPT_URL'] = 'invalid-rum-script-url';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        unset($_SERVER['RETRACE_RUM_SCRIPT_URL']);
    }

    public function testRumSetupConfigurationWithEmptyRumScriptUrlFromEnv()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $_SERVER['RETRACE_RUM_SCRIPT_URL'] = '';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $mockRumObject->setupConfiguration(
            $appName,
            $environment,
            $rumKey
        );

        $this->assertSame($mockRumObject->getRumScriptUrl(), self::DEFAULT_RUM_SCRIPT_URL);

        unset($_SERVER['RETRACE_RUM_SCRIPT_URL']);
    }

    public function testRumGetReportingUrlCli()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $_SERVER['PHP_SELF'] = '/var/www/Test.php';
        $mockRumObject = $this->givenARumObjectWithDefaultSetting();

        $this->assertSame($mockRumObject->getReportingUrl(), 'Test');
        unset($_SERVER['PHP_SELF']);
    }

    public function testRumGetReportingUrlNonCli()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $_SERVER['REQUEST_URI'] = '/api/users/?page=1';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getSapiName'])
            ->getMock();

        $mockRumObject->method('getSapiName')
            ->willReturn('non-cli');

        $this->assertSame($mockRumObject->getReportingUrl(), '/api/users');
        unset($_SERVER['REQUEST_URI']);
    }

    public function testRumGetReportingUrlNonCliWithRequestMethod()
    {
        $ds = DIRECTORY_SEPARATOR;
        $appName = 'test app name';
        $environment = 'test environment';
        $rumKey = 'valid-rum-key';
        $_SERVER['REQUEST_URI'] = '/api/books/?page=1';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $mockRumObject = $this->getMockBuilder(Rum::class)
            ->setMethods(['getSapiName'])
            ->getMock();

        $mockRumObject->method('getSapiName')
            ->willReturn('non-cli');

        $this->assertSame($mockRumObject->getReportingUrl(), 'GET-/api/books');
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    private function givenARumObjectWithDefaultSetting()
    {
        return new Rum;
    }

    /**
     * Clean up rum singleton instance
     *
     * @return void
     */
    private function cleanupRumInstance()
    {
        $singleton = Rum::getInstance(); // no idea what's inside
        $reflection = new \ReflectionClass($singleton);
        $instance = $reflection->getProperty('_instances');
        $instance->setAccessible(true); // now we can modify that :)
        $instance->setValue(null, null); // instance is gone
        $instance->setAccessible(false); // clean up
    }
}
