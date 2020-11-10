[![PHP version](https://badge.fury.io/ph/stackify%2Flogger.svg)](http://badge.fury.io/ph/stackify%2Flogger)

# Stackify PHP Logger 

Standalone Stackify PHP [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) Logger. 

* **Errors and Logs Overview:** http://support.stackify.com/errors-and-logs-overview/
* **Sign Up for a Trial:** http://www.stackify.com/sign-up/

## PHP Logging Framework Integrations

* **Monolog** Handler: https://github.com/stackify/stackify-log-monolog
* **log4php** Appender: https://github.com/stackify/stackify-log-log4php


## Installation

Install the latest version with `composer require stackify/logger`

### Installation with Linux Agent 

This is the suggested installation option, offering the best 
logging performance. 

```php
use Stackify\Log\Standalone\Logger;

$logger = new Logger('application_name', 'environment_name');
```

### Installation without Linux Agent

This option does not require the Stackify Agent to be installed because it sends data directly to Stackify services. It collects log entries in batches, calls curl using the ```exec``` function, and sends data to the background immediately [```exec('curl ... &')```]. This will affect the performance of your application minimally, but it requires permissions to call ```exec``` inside the PHP script and it may cause silent data loss in the event of any network issues. This transport method does not work on Windows. To configure ExecTransport you need to pass the environment name and API key (license key):
   
```php
use Stackify\Log\Transport\ExecTransport;
use Stackify\Log\Standalone\Logger;
    
$transport = new ExecTransport('api_key');
$logger = new Logger('application_name', 'environment_name', $transport);
```   

#### Optional Settings

**Proxy**
- ExecTransport supports data delivery through proxy. Specify proxy using [libcurl format](http://curl.haxx.se/libcurl/c/CURLOPT_PROXY.html): `<[protocol://][user:password@]proxyhost[:port]>`
```php
$transport = new ExecTransport($apiKey, ['proxy' => 'https://55.88.22.11:3128']);
```

**Curl path**
- It can be useful to specify ```curl``` destination path for ExecTransport. This option is set to 'curl' by default.
```php
$transport = new ExecTransport($apiKey, ['curlPath' => '/usr/bin/curl']);
```

**Log Server Environment Variables**
- Server environment variables can be added to error log message metadata. **Note:** This will log all 
system environment variables; do not enable if sensitive information such as passwords or keys are stored this way.

 ```php
$logger = new Logger('application_name', 'environment_name', $transport, true);
```

### **Configuration Settings**
- This allow users to override default settings of the logger (Masking Request Variables, Session, Cookie or Updating connection properties to different Transports etc.)
- **Note** - By default capturing raw post data `(e.g. file_get_contents("php://input"))` and `$_POST` variables are `DISABLED` by default 
    - To enable you can set the following options to `true`
    - `CapturePostVariables` - `Boolean` - Capture `$_POST` variables
    - `CaptureRawPostData` - `Boolean` - Capture `php://input` stream data `(e.g. file_get_contents("php://input"))`
        ```php
        $config = array(
                'CapturePostVariables' => true,
                'CaptureRawPostData' => true,
                ...
        );
        ```
- **Note** - For the `Whitelist/Blackist` setting. Anything `falsy` (`null`, `false`, `array()` etc. - Refer to php [empty](https://www.php.net/manual/en/function.empty.php) function checking) will be considered as `Do Not Track` - No variable data will be processed.

#### Logger Level
 ```php
$config = array(
        'CaptureServerVariables' => false,
        'CaptureServerVariablesWhitelist' => '*',
        'CaptureServerVariablesBlacklist' => 'REMOTE_ADDR,SERVER_ADDR',
        ...
    );

$logger = new Logger('application_name', 'environment_name', $transport, true, $config);
```

#### Transport Level
- This applies to all the transports `(ExecTransport, CurlTransport, AgentTransport, AgentSocketTransport)`
 ```php
$config = array(
        'CaptureServerVariables' => false,
        'CaptureServerVariablesWhitelist' => '*',
        'CaptureServerVariablesBlacklist' => 'REMOTE_ADDR,SERVER_ADDR',
        ...
    );

$transport = new ExecTransport($apiKey, [
    'config' => $config
]);
```
### Available Options:
#### Server Variables
- `CaptureServerVariables` - `Boolean` - Capture `$_SERVER` variables
- `CaptureServerVariablesWhitelist` - `Array` or `Comma-delimited string` - Whitelist `$_SERVER` attributes
- `CaptureServerVariablesBlacklist` - `Array` or `Comma-delimited string` - Mask `$_SERVER` attributes (e.g. `attribute => 'X-MASKED-X'`)
#### Get Variables
- `CaptureGetVariables` - `Boolean` - Capture `$_GET` variables
- `CaptureGetVariablesWhitelist` - `Array` or `Comma-delimited string` - Whitelist `$_GET` attributes
- `CaptureGetVariablesBlacklist` - `Array` or `Comma-delimited string` - Mask `$_GET` attributes (e.g. `attribute => 'X-MASKED-X'`)
#### Post Variables
- `CapturePostVariables` - `Boolean` - Capture `$_POST` variables
- `CapturePostVariablesWhitelist` - `Array` or `Comma-delimited string` - Whitelist `$_POST` attributes
- `CapturePostVariablesBlacklist` - `Array` or `Comma-delimited string` - Mask `$_POST` attributes (e.g. `attribute => 'X-MASKED-X'`)
#### Session Variables
- `CaptureSessionVariables` - `Boolean` - Capture `$_SESSION` variables
- `CaptureSessionVariablesWhitelist` - `Array` or `Comma-delimited string` - Whitelist `$_SESSION` attributes
- `CaptureSessionVariablesBlacklist` - `Array` or `Comma-delimited string` - Mask `$_SESSION` attributes (e.g. `attribute => 'X-MASKED-X'`)
#### Error Headers
- `CaptureErrorHeaders` - `Boolean` - Capture `HEADER` attributes available in `$_SERVER` variable
- `CaptureErrorHeadersWhitelist` - `Array` or `Comma-delimited string` - Whitelist `HEADER` attributes in `$_SERVER` variable
- `CaptureErrorHeadersBlacklist` - `Array` or `Comma-delimited string` - Mask `HEADER` attributes in `$_SERVER` variable (e.g. `attribute => 'X-MASKED-X'`)
#### Error Cookies
- `CaptureErrorCookies` - `Boolean` - Capture `$_COOKIE` variables
- `CaptureErrorCookiesWhitelist` - `Array` or `Comma-delimited string` - Whitelist `$_COOKIE` attributes
- `CaptureErrorCookiesBlacklist` - `Array` or `Comma-delimited string` - Mask `$_COOKIE` attributes
#### Capture Raw Post Data
- `CaptureRawPostData` - `Boolean` - Capture `php://input` stream data `(e.g. file_get_contents("php://input"))`
#### Debug Settings
- `Debug` - `Boolean` - Enable DEBUG in the logger
- `DebugLogPath` - `String` - A qualified path for the log file produced during debug or error
#### Agent Transport Settings
- `Protocol` - `String` - Protocol can be `tcp` or `udp`
- `Host` - `String` - Server Hostname
- `Port` - `Numeric` - Port
- `SocketTimeoutConnect` - `Numeric` - Connection Request Timeout
- `SocketTimeoutWrite` - `Numeric` - Connection Write Timeout
- `SocketMaxConnectAttempts` - `Numeric` - Connection Attempts
#### Agent Socket Transport Settings
- `DomainSocketPath` - `String` - Stackify Agent unix socket path
#### API or Curl Exec Socket Transport Settings
- `ApiBaseUrl` - `String` - Stackify API base url
- `ApiCallLogsEndpoint` - `String` - Stackify API Call Logs endpoint
- `ApiMaxTimeout` - `Numeric` - Stackify API Call Max Timeout
- `ApiVersionHeader` - `String` - Stackify API Version Header

#### Troubleshooting

If transport does not work, try looking into ```vendor\stackify\logger\src\Stackify\debug\log.log``` file (if it is available for writing). Errors are also written to global PHP [error_log](http://php.net/manual/en/errorfunc.configuration.php#ini.error-log).
Note that ExecTransport does not produce any errors at all, but you can switch it to debug mode:
```php
$transport = new ExecTransport($apiKey, ['debug' => true]);
```

You can set it also on the `Logger` level. Setting the `Debug` and `DebugLogPath`

```php
$config = array(
        'DebugLogPath' => '/path/to/log.log',
        'Debug' => true
    );

$logger = new Logger('application_name', 'environment_name', $transport, true, $config);
```

## License

Copyright 2019 Stackify, LLC.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
