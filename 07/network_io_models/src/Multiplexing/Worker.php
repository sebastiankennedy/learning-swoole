<?php

namespace NetworkIoModels\Multiplexing;

use Exception;

/**
 * Class Worker - IO 多路复用
 */
class Worker
{
    /**
     * @var null
     */
    public $onStart = null;
    /**
     * @var null
     */
    public $onReceive = null;
    /**
     * @var null
     */
    public $onConnect = null;
    /**
     * @var null
     */
    public $onClose = null;
    /**
     * @var false|resource|null
     */
    public $socket = null;
    /**
     * @var false|resource|null
     */
    public $socketList = [];

    public function __construct($socket_address)
    {
        if (($this->socket = stream_socket_server($socket_address, $errNo, $errString)) === false) {
            throw new Exception('Could not create listening socket: ' . $errNo . ' - ' . $errString);
        }

        // 设置非阻塞
        stream_set_blocking($this->socket, false);

        // (int) 将资源转换成套接字描述符
        $this->socketList[] = $this->socket;
    }

    public function start()
    {
        if (is_callable($this->onStart)) {
            ($this->onStart)();
        }

        $this->accept();
    }

    public function accept()
    {
        while (true) {
            $readableSockets = $this->socketList;

            // 轮询连接，有数据可读可写时，调用 IO 操作函数
            stream_select($readableSockets, $write, $except, 5);

            foreach ($readableSockets as $socket) {
                // 如果服务端 Socket 可读，说明有新的客户端连接
                if ($socket === $this->socket) {
                    $client = stream_socket_accept($this->socket);

                    if ($client) {
                        // 回调监听连接函数
                        if (is_callable($this->onConnect)) {
                            ($this->onConnect)($this, $client);
                        }

                        $this->socketList[] = $client;
                    }
                } else {
                    // 如果客户端 Socket 可读，说明有新的客户端消息
                    $data = stream_socket_recvfrom($socket, 1024 * 1024 * 2);

                    if ($data) {
                        if (is_callable($this->onReceive)) {
                            ($this->onReceive)($this, $socket, $data);
                        }
                    }
                }
            }
        }

        stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);
    }
}