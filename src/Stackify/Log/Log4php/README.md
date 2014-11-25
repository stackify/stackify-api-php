stackify-log-monolog
================

Apache log4php appender for sending log messages and exceptions to Stackify.
Apache log4php >= 2.2.0 is supported.

Errors and Logs Overview:
http://docs.stackify.com/m/7787/l/189767

Sign Up for a Trial:
http://www.stackify.com/sign-up/

## Installation
Install the latest version with `composer require stackify/log4php`

Or add dependency to `composer.json` file:
```json
    "stackify/log4php": "~1.0",
```

By default handler requires [Stackify agent](https://stackify.screenstepslive.com/s/3095/m/7787/l/119709-installation-for-linux) to be running. There are other ways to send data to Stackify, read about pros and cons in [transports](#transport) section.

## Basic usage
Using XML-configuration:
```xml
<configuration xmlns="http://logging.apache.org/log4php/">
    <appender name="stackifyAppender" class="\Stackify\Log\Log4php\Appender">
        <param name="appName" value="application_name" />
    </appender>
    <root>
        <level value="TRACE" />
        <appender_ref ref="stackifyAppender" />
    </root>
</configuration>
```
```php
Logger::configure('config.xml');
$logger = Logger::getLogger('logger_name');
$logger->debug('log4php debug');
```

Using PHP-configuration:
```php
$config = array(
    'rootLogger' => array(
        'appenders' => array('stackify'),
    ),
    'appenders' => array(
        'stackify' => array(
            'class' => '\Stackify\Log\Log4php\Appender',
            'params' => array(
            	'appName' => 'application_name',
            ),
        ),
    ),
);
Logger::configure($config);
$logger = Logger::getLogger('logger_name');
$logger->warn('warning message');
```

## <a name="transport"></a>Transport options
Handler supports three ways to deliver data to Stackify:

- <b>AgentTransport</b> is used by default and it does not require additional configuration on PHP side. All data is be passed to [Stackify agent](https://stackify.screenstepslive.com/s/3095/m/7787/l/119709-installation-for-linux), which must be installed on the same machine. Local TCP socket is used, so performance of your application is affected minimally.
- <b>ExecTransport</b> does not require Stackify agent to be installed, because it sends data directly to Stackify services. It collects log entries in a single batch, calls curl using ```exec``` function and sends it to background immediately [```exec('curl ... &')```]. This way influences performance of your application minimally, but it requires permissions to call ```exec``` inside PHP script and it may cause silent data loss in case of network issues. This transport does not work on Windows. To configure ExecTransport you need to pass environment name and API key (license key):

    ```xml
    <appender name="stackifyAppender" class="\Stackify\Log\Log4php\Appender">
        <param name="appName" value="application_name" />
        <param name="environmentName" value="environment_name" />
        <param name="mode" value="exec" />
        <param name="apiKey" value="api_key" />
    </appender>
    ```
- <b>CurlTransport</b> does not require Stackify agent to be installed, it also sends data directly to Stackify services. It collects log entries in a single batch and sends data using native [PHP cURL](http://php.net/manual/en/book.curl.php) functions. This way is a blocking one, so it should not be used on production environments. To configure CurlTransport you need to pass environment name and API key (license key):

    ```xml
    <appender name="stackifyAppender" class="\Stackify\Log\Log4php\Appender">
        <param name="appName" value="application_name" />
        <param name="environmentName" value="environment_name" />
        <param name="mode" value="curl" />
        <param name="apiKey" value="api_key" />
    </appender>
    ```

## Configuration
#### Proxy
ExecTransport and CurlTransport support data delivery through proxy. Specify proxy using [libcurl format](http://curl.haxx.se/libcurl/c/CURLOPT_PROXY.html): <[protocol://][user:password@]proxyhost[:port]>

    ```xml
    <param name="proxy" value="https://55.88.22.11:3128" />
    ```

#### Curl path
It can be useful to specify ```curl``` destination path for ExecTransport. This option is set to 'curl' by default.

    ```xml
    <param name="curlPath" value="/usr/bin/curl" />
    ```

#### Agent port
By default AgentTransport uses port number ```10515```. To change it pass new port number:

    ```xml
    <param name="port" value="10516" />
    ```

## Troubleshooting
If transport does not work, try looking into ```vendor\stackify\logger\src\Stackify\debug\log.log``` file. Errors are also written to global PHP [error_log](http://php.net/manual/en/errorfunc.configuration.php#ini.error-log).
Note that ExecTransport does not produce any errors at all, but you can switch it to debug mode:

    ```xml
    <param name="debug" value="1" />
    ```

## License

Copyright 2014 Stackify, LLC.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.