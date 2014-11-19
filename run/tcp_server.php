<?php

$server = stream_socket_server('tcp://127.0.0.1:1234', $errno, $errorMessage);

if ($server === false) {
    die("Could not bind to socket: $errorMessage");
}

$clientId = 0;
for (;;) {
    $clientId++;
    $messageId = 0;
    $client = @stream_socket_accept($server);
    if ($client) {
        $data = '';
        while (!feof($client)) {
            $data .= fread($client, 1024);
        }
        $logs = explode("\n", $data);
        foreach ($logs as $log) {
            if ('' === $log) {
                continue;
            }
            // format JSON message
            $message = json_encode(json_decode($log), JSON_PRETTY_PRINT);
            $messageId++;
            echo "Client: $clientId\n";
            echo "Message: $messageId\n";
            echo "Data:\n$message\n\n";
        }
        fclose($client);
    }
}