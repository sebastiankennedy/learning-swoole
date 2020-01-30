<?php

require '../config.php';

$client = stream_socket_client('tcp://' . IP_ADDRESS . ':' . PORT);
$now = time();

fwrite($client, 'Hello Server');
$data = fread($client, 65535);
fclose($client);

echo $data . PHP_EOL;