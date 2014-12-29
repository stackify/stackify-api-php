stackify-api-php
================

Common libraries for [Stackify Monolog handler](https://github.com/stackify/stackify-log-monolog) and [Stackify log4php appender](https://github.com/stackify/stackify-log-log4php).
This package also includes a standalone [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) -compatible logger that can be used without third-party libraries.

Errors and Logs Overview:
http://docs.stackify.com/m/7787/l/189767

Sign Up for a Trial:
http://www.stackify.com/sign-up/

## Standalone logger
Install the latest version with `composer require stackify/logger`

```php
use Stackify\Log\Standalone\Logger;

$logger = new Logger('appname.com');
$logger->warning('something happened');
try {
    $db->connect();
catch (DbException $ex) {
    $logger->error('DB is not available', ['ex' => $ex]);
}
```

By default handler requires [Stackify agent](https://stackify.screenstepslive.com/s/3095/m/7787/l/119709-installation-for-linux) to be running.
There are other ways to send data, read more in [Monolog package documentation](https://github.com/stackify/stackify-log-monolog),
all transports are available for standalone logger as well.


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