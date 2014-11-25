stackify-log-monolog
================

Monolog handler for sending log messages and exceptions to Stackify.
Monolog >= 1.1.0 is supported.

Errors and Logs Overview:
http://docs.stackify.com/m/7787/l/189767

Sign Up for a Trial:
http://www.stackify.com/sign-up/

## Installation
Install the latest version with `composer require stackify/monolog`

Or add dependency to `composer.json` file:
```json
    "stackify/monolog": "~1.0",
```

By default handler requires [Stackify agent](https://stackify.screenstepslive.com/s/3095/m/7787/l/119709-installation-for-linux) to be running. There are other ways to send data to Stackify, read about pros and cons in [transports](#transport) section.

## Basic usage
```php
use Monolog\Logger;
use Stackify\Log\Monolog\Handler as StackifyHandler;

$handler = new StackifyHandler('application_name');
$logger = new Logger('log_channel');
$logger->pushHandler($handler);
$logger->warning('something happened');
```

If you use [MonologBundle](https://github.com/symfony/MonologBundle) it makes sence to configure Stackify handler using Symfony DependencyInjection configuration files:
```yml
# YML example
services:
    stackify_handler:
        class: "Stackify\\Log\\Monolog\\Handler"
        arguments: ["application_name"]

monolog:
    handlers:
        main:
            type:   stream
            path:   "%kernel.logs_dir%/%kernel.environment%.log"
        stackify:
            type:   service
            id:     stackify_handler
```

To get more error details pass Exception objects to logger if available:
```php
try {
    $db->connect();
catch (DbException $ex) {
    // you may use any key name
    $logger->error('DB is not available', ['ex' => $ex]);
}
```

## <a name="transport"></a>Transport options
Handler supports three ways to deliver data to Stackify:

- <b>AgentTransport</b> is used by default and it does not require additional configuration on PHP side. All data is be passed to [Stackify agent](https://stackify.screenstepslive.com/s/3095/m/7787/l/119709-installation-for-linux), which must be installed on the same machine. Local TCP socket is used, so performance of your application is affected minimally.
- <b>ExecTransport</b> does not require Stackify agent to be installed, because it sends data directly to Stackify services. It collects log entries in a single batch, calls curl using ```exec``` function and sends it to background immediately [```exec('curl ... &')```]. This way influences performance of your application minimally, but it requires permissions to call ```exec``` inside PHP script and it may cause silent data loss in case of network issues. This transport does not work on Windows. To configure ExecTransport you need to pass environment name and API key (license key):
    ```php
    use Stackify\Log\Transport\ExecTransport;
    use Stackify\Log\Monolog\Handler as StackifyHandler;
    
    $transport = new ExecTransport('api_key');
    $handler = new StackifyHandler('application_name', 'environment_name', $transport);
    ```
    ```yml
    # or configuration file example
    services:
        stackify_transport:
            class: "Stackify\\Log\\Transport\ExecTransport"
            arguments: ["api_key"]
        stackify_handler:
            class: "Stackify\\Log\\Monolog\\Handler"
            arguments: ["application_name", "environment_name", "@stackify_transport"]
    ```
- <b>CurlTransport</b> does not require Stackify agent to be installed, it also sends data directly to Stackify services. It collects log entries in a single batch and sends data using native [PHP cURL](http://php.net/manual/en/book.curl.php) functions. This way is a blocking one, so it should not be used on production environments. To configure CurlTransport you need to pass environment name and API key (license key):
    ```php
    use Stackify\Log\Transport\CurlTransport;
    use Stackify\Log\Monolog\Handler as StackifyHandler;
    
    $transport = new CurlTransport('api_key');
    $handler = new StackifyHandler('application_name', 'environment_name', $transport);
    ```
    ```yml
    # or configuration file example
    services:
        stackify_transport:
            class: "Stackify\\Log\\Transport\CurlTransport"
            arguments: ["api_key"]
        stackify_handler:
            class: "Stackify\\Log\\Monolog\\Handler"
            arguments: ["application_name", "environment_name", "@stackify_transport"]
    ```

## Configuration
#### Proxy
ExecTransport and CurlTransport support data delivery through proxy. Specify proxy using [libcurl format](http://curl.haxx.se/libcurl/c/CURLOPT_PROXY.html): <[protocol://][user:password@]proxyhost[:port]>
```php
$transport = new ExecTransport($apiKey, ['proxy' => 'https://55.88.22.11:3128']);
```

#### Curl path
It can be useful to specify ```curl``` destination path for ExecTransport. This option is set to 'curl' by default.
```php
$transport = new ExecTransport($apiKey, ['curlPath' => '/usr/bin/curl']);
```

#### Agent port
By default AgentTransport uses port number ```10515```. To change it pass new port number:
```php
$transport = new AgentTransport(['port' => 10516]);
$handler = new StackifyHandler('application_name', 'environment_name', $transport);
```

## Troubleshooting
If transport does not work, try looking into ```vendor\stackify\logger\src\Stackify\debug\log.log``` file. Errors are also written to global PHP [error_log](http://php.net/manual/en/errorfunc.configuration.php#ini.error-log).
Note that ExecTransport does not produce any errors at all, but you can switch it to debug mode:
```php
$transport = new ExecTransport($apiKey, ['debug' => true]);
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