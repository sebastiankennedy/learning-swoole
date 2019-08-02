<?php

namespace Http;

use Swoole\Http\Server;

class App
{
    protected static $rootPath;
    // 路径
    protected static $frameworkPath;
    protected static $applicationPath;
    protected $server;
    // 监听目录
    protected $watchPath;
    // 目录散列值
    protected $md5File;

    public function run()
    {
        // 配置热加载目录
        self::$rootPath = dirname(dirname(__DIR__));
        self::$frameworkPath = self::$rootPath . '/framework';
        self::$applicationPath = self::$rootPath . '/application';
        $this->watchPath = [self::$frameworkPath, self::$applicationPath];

        // 初始化监控文件散列值
        $this->md5File = $this->getMd5();

        // 启动 HTTP 服务
        $this->server = new Server('0.0.0.0', 9190);

        // HTTP 服务配置
        $this->server->set([
            'worker_num' => 1,
            'pack_max_length' => 1024 * 1024 * 1,
        ]);

        // 绑定事件
        $this->server->on('Start', [$this, 'start']);
        $this->server->on('request', [$this, 'request']);
        $this->server->on('workerStart', [$this, 'workerStart']);
        $this->server->start();
    }

    public function getMd5()
    {
        $md5 = '';
        // 对比当前文件散列值和上次文件散列值
        foreach ($this->watchPath as $dir) {
            $md5 .= self::md5File($dir);
        }

        return $md5;
    }

    public static function md5File($dir)
    {
        // 遍历文件夹当中的所有文件,得到所有的文件的md5散列值
        if (! is_dir($dir)) {
            return '';
        }
        $md5File = [];
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {

            if ($entry !== '.' && $entry !== '..') {
                if (is_dir($dir . '/' . $entry)) {
                    //递归调用
                    $md5File[] = self::md5File($dir . '/' . $entry);
                } elseif (substr($entry, -4) === '.php') {
                    $md5File[] = md5_file($dir . '/' . $entry);
                }
                $md5File[] = $entry;
            }
        }
        $d->close();
        return md5(implode('', $md5File));
    }

    public function start($server)
    {
        var_dump('Event: start');
        $this->reload();
    }

    public function reload()
    {
        swoole_timer_tick(3000, function () {
            $md5 = $this->getMd5();
            var_dump('Event: reload, md5 value is ' . $md5);
            if ($md5 != $this->md5File) {
                // 重启主进程
                $this->server->reload();
                // 重新赋值
                $this->md5File = $md5;
            }
        });
    }

    public function request($request, $response)
    {
        $uri = $request->server['request_uri'];
        if ($uri === '/favicon.ico') {
            $response->status(404);
            $response->end();
        }
        $response->end('Hello World!');
    }

    public function workerStart($server, $workerId)
    {
    }
}

