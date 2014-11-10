<?php

$url = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
list($handler,) = explode('?', $url);
$filename = "$handler.php";

if (empty($handler)) {
?>

<ul>
    <li><a href="/standalone?some=data&foo=bar">Direct logger</a></li>
    <li><a href="/monolog?some=data&foo=bar">Monolog</a></li>
    <li><a href="/log4php?some=data&foo=bar">Log4php</a></li>
</ul>

<?php
} elseif (file_exists($filename)) {
    include $filename;
} else {
    die("Handler for '$handler' does not exist");
}