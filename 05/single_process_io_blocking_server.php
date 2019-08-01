<?php

require_once '../config.php';

class SingleWorker
{
    public $onConnect = null;
    public $onMessage = null;
    protected $socket = null;

    public function __construct($protocol, $ipAddress, $port)
    {
        // 绑定协议、IP 地址、端口
        $this->socket = stream_socket_server(strtolower($protocol) . "://$ipAddress:$port");
    }

    public function start()
    {
        // Accept 阻塞进程，监听客户端连接
        while (true) {
            $clientSocket = stream_socket_accept($this->socket);

            // 触发连接事件回调
            if (! empty($clientSocket) && is_callable($this->onConnect)) {
                call_user_func($this->onConnect, $clientSocket);
            }

            // 读取客户端请求内容
            $buffer = fread($clientSocket, 65535);

            // 触发内度读取事件回调
            if (! empty($buffer) && is_callable($this->onMessage)) {
                call_user_func($this->onMessage, $clientSocket);
            }

            // 关闭客户端连接
            if ($clientSocket) {
                fclose($clientSocket);
            }
        }
    }
}

$worker = new SingleWorker('TCP', HOST, PORT);
$worker->onConnect = function ($fd) {
    echo "客户端 " . $fd . '连接成功。' . PHP_EOL;
};
$worker->onMessage = function ($fd) {
    $content = 'Hello World';
    $httpResponse = "HTTP/1.1 200 OK\r\n";
    $httpResponse .= "Content-Type: text/html;charset=UTF-8\r\n";
    $httpResponse .= "Server: php socket server\r\n";
    $httpResponse .= "Content-length: " . strlen($content) . "\r\n\r\n";
    $httpResponse .= $content;
    fwrite($fd, $httpResponse);
};
$worker->start();