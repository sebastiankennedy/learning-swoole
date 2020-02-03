<?php

require __DIR__ . '/../../../../config.php';

$client = stream_socket_client(TCP_ID_ADDRESS_PORT);

stream_socket_sendto($client, "Hello, Server.");
$boolean = true;
while ($boolean) {
    $data = stream_socket_recvfrom($client, 1024 * 1024 * 2);
    if ($data) {
        print_r($data);
        $boolean = false;
    }
}

stream_socket_shutdown($client, STREAM_SHUT_RDWR);