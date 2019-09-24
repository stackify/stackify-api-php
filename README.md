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

 
#### Troubleshooting

If transport does not work, try looking into ```vendor\stackify\logger\src\Stackify\debug\log.log``` file (if it is available for writing). Errors are also written to global PHP [error_log](http://php.net/manual/en/errorfunc.configuration.php#ini.error-log).
Note that ExecTransport does not produce any errors at all, but you can switch it to debug mode:
```php
$transport = new ExecTransport($apiKey, ['debug' => true]);
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
