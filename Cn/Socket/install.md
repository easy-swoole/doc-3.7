---
title: easyswoole socket
meta:
  - name: description
    content: easyswoole/socket
  - name: keywords
    content: easyswoole socket|swoole tcp udp websocket
---

# Socket 控制器

## 组件安装

> composer require easyswoole/socket

## 使用示例

关于 `Socket` 控制器使用的具体示例，请查看 [demo](https://github.com//Stitch-June/EasySwooleSocketDemo)

## tcp socket 控制器

需要将配置文件 `dev.php/produce.php` 中的 `SERVER_TYPE` 配置项修改为 `EASYSWOOLE_SERVER`。或自行添加 `tcp`
子服务，在子服务中处理 `tcp socket` 控制器的调度。

### 1.创建控制器类

在 `App` 目录下创建 `TcpController` 目录，然后创建 `Index.php` 文件，然后编写如下代码：

```php
<?php
namespace App\TcpController;

use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->response()->setMessage('this is index');
    }
}
```

### 2.创建协议解析器类 Parser

在 `App` 目录下创建 `Parser` 目录，然后创建 `TcpParser.php` 文件，然后编写如下代码：

```php
<?php
namespace App\Parser;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class TcpParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        $data       = substr($raw, 4);
        $data       = json_decode($data, true);
        $caller     = new Caller();
        $controller = !empty($data['controller']) ? $data['controller'] : 'Index';
        $action     = !empty($data['action']) ? $data['action'] : 'index';
        $param      = !empty($data['param']) ? $data['param'] : [];
        $controller = "App\\TcpController\\{$controller}";
        $caller->setControllerClass($controller);
        $caller->setAction($action);
        $caller->setArgs($param);
        return $caller;
    }

    public function encode(Response $response, $client): ?string
    {
        return pack('N', strlen(strval($response->getMessage()))) . $response->getMessage();
    }
}
```

### 3.注册 Dispatcher

需要在 `mainServerCreate` 事件中注册 `Dispatcher`，即在项目根目录的 `EasySwooleEvent.php` 文件的 `mainServerCreate`
方法进行注册，注册示例如下：

```php
<?php
namespace EasySwoole\EasySwoole;

use App\Parser\TcpParser;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;
use Throwable;
use EasySwoole\Socket\Client\Tcp;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $register->set($register::onConnect, function (Server $server, int $fd) {
            echo "Client: Connect. fd: {$fd}\n";
        });
    
        // 注册 Dispatcher
        $config = new SocketConfig();
        $config->setType($config::TCP);
        $config->setParser(TcpParser::class);
        $dispatcher = new Dispatcher($config);
        $config->setOnExceptionHandler(function (Server $server, Throwable $throwable, string $raw, Tcp $client, Response $response) {
            $response->setMessage('system error!');
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE);
        });
        $register->set($register::onReceive, function (Server $server, int $fd, int $reactorId, string $data) use ($dispatcher) {
            $dispatcher->dispatch($server, $data, $fd, $reactorId);
        });
        
        $register->set($register::onClose, function (Server $server, int $fd) {
            echo "Client: Close. fd: {$fd}\n";
        });
    }
}
```

### 4.启动服务及测试

启动主服务，然后添加 `TcpClient` 进行测试，在 `App` 目录下创建 `Client` 目录，然后创建 `TcpClient.php` 文件，然后编写如下代码：

```php
<?php
declare(strict_types=1);

namespace App\Client;

use function Swoole\Coroutine\run;
use Swoole\Coroutine\Client;

class TcpClient
{
    public static function test()
    {
        run(function () {
            $client = new Client(SWOOLE_TCP);
            $client->set([
                'open_length_check'     => true,
                'package_max_length'    => 81920,
                'package_length_type'   => 'N',
                'package_length_offset' => 0,
                'package_body_offset'   => 4,
            ]);

            if (!$client->connect('127.0.0.1', 9501)) {
                echo 'tcp connect fail!';
            }

            $sendBody = json_encode([
                'controller' => 'Index',
                'action'     => 'index'
            ]);
            $client->send(pack('N', strlen($sendBody)) . $sendBody);
            $recvBody = $client->recv();
            $len      = unpack('N', $recvBody)[1];

            var_dump(substr($recvBody, 4, $len));
        });
    }
}

TcpClient::test();
```

测试结果如下：

```bash
$ php App/Client/TcpClient.php 
string(13) "this is index"
```

## udp socket 控制器

需要将配置文件 `dev.php/produce.php` 中的 `SERVER_TYPE` 配置项修改为 `EASYSWOOLE_SERVER`，`SOCK_TYPE`
配置项修改为 `SWOOLE_UDP`。或自行添加 `udp` 子服务，在子服务中处理 `udp socket` 控制器的调度。

### 1.创建控制器类

在 `App` 目录下创建 `UdpController` 目录，然后创建 `Index.php` 文件，然后编写如下代码：

```php
<?php
namespace App\UdpController;

use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->response()->setMessage('this is index');
    }
}
```

### 2.创建协议解析器类 Parser

在 `App` 目录下创建 `Parser` 目录，然后创建 `UdpParser.php` 文件，然后编写如下代码：

```php
<?php
namespace App\Parser;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class UdpParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        $data       = json_decode($raw, true);
        $caller     = new Caller();
        $controller = !empty($data['controller']) ? $data['controller'] : 'Index';
        $action     = !empty($data['action']) ? $data['action'] : 'index';
        $param      = !empty($data['param']) ? $data['param'] : [];
        $controller = "App\\UdpController\\{$controller}";
        $caller->setControllerClass($controller);
        $caller->setAction($action);
        $caller->setArgs($param);
        return $caller;
    }

    public function encode(Response $response, $client): ?string
    {
        return json_encode($response->getMessage());
    }
}
```

### 3.注册 Dispatcher

需要在 `mainServerCreate` 事件中注册 `Dispatcher`，即在项目根目录的 `EasySwooleEvent.php` 文件的 `mainServerCreate`
方法进行注册，注册示例如下：

```php
<?php
namespace EasySwoole\EasySwoole;

use App\Parser\UdpParser;
use EasySwoole\Socket\Client\Udp;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;
use Throwable;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 注册 Dispatcher
        $config = new SocketConfig();
        $config->setType($config::UDP);
        $config->setParser(UdpParser::class);
        $dispatcher = new Dispatcher($config);
        $config->setOnExceptionHandler(function (Server $server, Throwable $throwable, string $raw, Udp $client, Response $response) {
            $response->setMessage('system error!');
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE);
        });
        $register->set($register::onPacket, function (Server $server, string $data, array $clientInfo) use ($dispatcher) {
            $dispatcher->dispatch($server, $data, $clientInfo['server_socket'], $clientInfo['address'], $clientInfo['port']);
        });
    }
}
```

### 4.启动服务及测试

启动主服务，然后添加 `UdpClient` 进行测试，在 `App` 目录下创建 `Client` 目录，然后创建 `UdpClient.php` 文件，然后编写如下代码：

```php
<?php
declare(strict_types=1);

namespace App\Client;

use function Swoole\Coroutine\run;
use Swoole\Coroutine\Client;

class UdpClient
{
    public static function test()
    {
        run(function () {
            $client = new Client(SWOOLE_UDP);
            $sendBody = json_encode([
                'controller' => 'Index',
                'action'     => 'index'
            ]);
            $client->sendto('127.0.0.1', 9501, $sendBody);
            $recvBody = $client->recv();

            var_dump(json_decode($recvBody));
        });
    }
}

UdpClient::test();
```

测试结果如下：

```bash
$ php App/Client/UdpClient.php 
string(13) "this is index"
```

## websocket 控制器

需要将配置文件 `dev.php/produce.php` 中的 `SERVER_TYPE` 配置项修改为 `EASYSWOOLE_WEB_SOCKET_SERVER`，`SOCK_TYPE`
配置项修改为 `SWOOLE_TCP`。或自行添加 `websocket` 子服务，在子服务中处理 `websocket` 控制器的调度。

### 1.创建控制器类

在 `App` 目录下创建 `WebSocketController` 目录，然后创建 `Index.php` 文件，然后编写如下代码：

```php
<?php
namespace App\UdpController;

use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->response()->setMessage('this is index');
    }
}
```

### 2.创建协议解析器类 Parser

在 `App` 目录下创建 `Parser` 目录，然后创建 `UdpParser.php` 文件，然后编写如下代码：

```php
<?php
namespace App\Parser;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class WebSocketParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        $data       = json_decode($raw, true);
        $caller     = new Caller();
        $controller = !empty($data['controller']) ? $data['controller'] : 'Index';
        $action     = !empty($data['action']) ? $data['action'] : 'index';
        $param      = !empty($data['param']) ? $data['param'] : [];
        $controller = "App\\WebSocketController\\{$controller}";
        $caller->setControllerClass($controller);
        $caller->setAction($action);
        $caller->setArgs($param);
        return $caller;
    }

    public function encode(Response $response, $client): ?string
    {
        return json_encode($response->getMessage());
    }
}
```

### 3.注册 Dispatcher

需要在 `mainServerCreate` 事件中注册 `Dispatcher`，即在项目根目录的 `EasySwooleEvent.php` 文件的 `mainServerCreate`
方法进行注册，注册示例如下：

```php
<?php

namespace EasySwoole\EasySwoole;

use App\Parser\WebSocketParser;
use EasySwoole\Socket\Client\WebSocket;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;
use Swoole\WebSocket\Frame;
use Throwable;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use Swoole\Http\Request as SwooleHttpRequest;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $register->set($register::onOpen, function (SwooleWebSocketServer $server, SwooleHttpRequest $request) {
            echo "Client: Open. fd: {$request->fd}\n";
        });
    
        // 注册 Dispatcher
        $config = new SocketConfig();
        $config->setType($config::WEB_SOCKET);
        $config->setParser(WebSocketParser::class);
        $dispatcher = new Dispatcher($config);
        $config->setOnExceptionHandler(function (Server $server, Throwable $throwable, string $raw, WebSocket $client, Response $response) {
            $response->setMessage('system error!');
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE);
        });

        $register->set($register::onMessage, function (SwooleWebSocketServer $server, Frame $frame) use ($dispatcher) {
            $dispatcher->dispatch($server, $frame->data, $frame);
        });
        
        $register->set($register::onClose, function (SwooleWebSocketServer $server, int $fd) {
            echo "Client: Close. fd: {$fd}\n";
        });
    }
}
```

### 4.启动服务及测试

启动主服务，然后添加 `WebSocketClient` 进行测试，在 `App` 目录下创建 `Client` 目录，然后创建 `WebSocketClient.php`
文件，然后编写如下代码：

```php
<?php
declare(strict_types=1);

namespace App\Client;

use Swoole\Coroutine\Http\Client;
use function Swoole\Coroutine\run;

class WebSocketClient
{
    public static function test()
    {
        run(function () {
            $client = new Client('127.0.0.1', 9501);
            $ret    = $client->upgrade('/');
            if ($ret) {
                $sendBody = json_encode([
                    'controller' => 'Index',
                    'action'     => 'index'
                ]);
                $client->push($sendBody);

                var_dump($client->recv());
            }
        });
    }
}

WebSocketClient::test();
```

测试结果如下：

```bash
$ php App/Client/WebSocketClient.php 
object(Swoole\WebSocket\Frame)#5 (5) {
  ["fd"]=>
  int(6)
  ["data"]=>
  string(15) ""this is index""
  ["opcode"]=>
  int(1)
  ["flags"]=>
  int(1)
  ["finish"]=>
  bool(true)
}
```

### 5.自定义 WebSocket 握手行为

添加 `WebSocket` 事件，编写代码如下：

```php
<?php
/**
 * description
 * author: longhui.huang <1592328848@qq.com>
 * datetime: 2024/6/12 10:35
 */
declare(strict_types=1);

namespace App;

use Swoole\Http\Request;
use Swoole\Http\Response;

class WebSocketEvent
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     */
    public function onHandShake(Request $request, Response $response)
    {
        /** 此处自定义握手规则 返回 false 时中止握手 */
        if (!$this->customHandShake($request, $response)) {
            $response->end();
            return false;
        }

        /** 此处是  RFC规范中的WebSocket握手验证过程 必须执行 否则无法正确握手 */
        if ($this->secWebsocketAccept($request, $response)) {
            $response->end();
            return true;
        }

        $response->end();
        return false;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     */
    protected function customHandShake(Request $request, Response $response): bool
    {
        /**
         * 这里可以通过 http request 获取到相应的数据
         * 进行自定义验证后即可
         * (注) 浏览器中 JavaScript 并不支持自定义握手请求头 只能选择别的方式 如get参数
         */
        $headers = $request->header;
        $cookie  = $request->cookie;

        // if (如果不满足我某些自定义的需求条件，返回false，握手失败) {
        //    return false;
        // }

        return true;
    }

    /**
     * RFC规范中的WebSocket握手验证过程
     * 以下内容必须强制使用
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     */
    protected function secWebsocketAccept(Request $request, Response $response): bool
    {
        // ws rfc 规范中约定的验证过程
        if (!isset($request->header['sec-websocket-key'])) {
            // 需要 Sec-WebSocket-Key 如果没有拒绝握手
            var_dump('shake fail 3');
            return false;
        }

        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key'])
            || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))
        ) {
            // 不接受握手
            var_dump('shake fail 4');
            return false;
        }

        $key     = base64_encode(sha1($request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $headers = array(
            'Upgrade'               => 'websocket',
            'Connection'            => 'Upgrade',
            'Sec-WebSocket-Accept'  => $key,
            'Sec-WebSocket-Version' => '13',
            'KeepAlive'             => 'off',
        );

        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        // 发送验证后的header
        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }

        // 接受握手 还需要101状态码以切换状态
        $response->status(101);
        var_dump('shake success at fd :' . $request->fd);
        return true;
    }
}
```

注册握手事件，在 `mainServerCreate` 事件中进行注册，示例如下：

```php
<?php
namespace EasySwoole\EasySwoole;

use App\Parser\WebSocketParser;
use App\WebSocketEvent;
use EasySwoole\Socket\Client\WebSocket;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;
use Swoole\Http\Request;
use Swoole\Server;
use Swoole\WebSocket\Frame;
use Throwable;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 注册 Dispatcher
        $config = new SocketConfig();
        $config->setType($config::WEB_SOCKET);
        $config->setParser(WebSocketParser::class);
        $dispatcher = new Dispatcher($config);
        $config->setOnExceptionHandler(function (Server $server, Throwable $throwable, string $raw, WebSocket $client, Response $response) {
            $response->setMessage('system error!');
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE);
        });

        ### 注册自定义握手事件
        $websocketEvent = new WebSocketEvent();
        $register->set(EventRegister::onHandShake, function (Request $request, \Swoole\Http\Response $response) use ($websocketEvent) {
            $websocketEvent->onHandShake($request, $response);
        });

        $register->set($register::onMessage, function (SwooleWebSocketServer $server, Frame $frame) use ($dispatcher) {
            $dispatcher->dispatch($server, $frame->data, $frame);
        });
        
        $register->set($register::onClose, function (SwooleWebSocketServer $server, int $fd) {
            echo "Client: Close. fd: {$fd}\n";
        });
    }
}
```

## socket 控制器对象

### 创建控制器类

控制器类需继承 `\EasySwoole\Socket\AbstractInterface\Controller`，示例如下：

```php
use EasySwoole\Socket\AbstractInterface\Controller;

class Test extends Controller
{
    public function index()
    {
        $this->response()->setMessage('this is index');
    }
}
```

### 自定义解析器 Parser

解析器需实现 `\EasySwoole\Socket\AbstractInterface\ParserInterface` 接口，示例如下：

```php
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class TestParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        // your code
        // todo::
    }

    public function encode(Response $response, $client): ?string
    {
        // your code
        // todo::
    }
}
```

### 注册 Dispatcher

需要在 `EasySwooleEvent` 中 [mainServerCreate](/FrameDesign/event.html#mainServerCreate)
事件中进行回调注册，下面是以 `tcp socket` 控制器为示例，具体使用示例如下：

```php
<?php

namespace EasySwoole\EasySwoole;

use App\Parser\TestParser;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 注册 Dispatcher
        $socketConfig = new SocketConfig();
        $socketConfig->setType(SocketConfig::TCP);
        $socketConfig->setParser(new TestParser());
        // 设置解析异常时的回调,默认将抛出异常到服务器
        $socketConfig->setOnExceptionHandler(function ($server, $throwable, $raw, $client, Response $response) {
            $response->setMessage("服务器异常（客户端fd:{$client->getFd()}）");
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE); // 发送完主动关闭该连接
        });
        $dispatch = new Dispatcher($socketConfig);
        
        $register->set($register::onConnect, function (Server $server, int $fd, int $reactor_id) {
            echo "fd {$fd} connected\n";
        });

        $register->set($register::onReceive, function (Server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
            $dispatch->dispatch($server, $data, $fd, $reactor_id);
        });

        $register->set($register::onClose, function (Server $server, int $fd, int $reactor_id) {
            echo "fd {$fd} closed\n";
        });
    }
}
```

在子服务中注册 `Dispatcher` 进行调度（在主服务为 `http` 或 `websocket` 或 `tcp` 服务的前提下），示例如下：

```php
<?php
namespace EasySwoole\EasySwoole;

use App\Parser\TestParser;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 注册 Dispatcher
        $server  = ServerManager::getInstance()->getSwooleServer();
        $subPort = $server->addlistener('0.0.0.0', 9502, SWOOLE_TCP);
        $subPort->set(
            // swoole 相关配置
        );

        $socketConfig = new SocketConfig();
        $socketConfig->setType($socketConfig::TCP);
        $socketConfig->setParser(new TestParser());
        // 设置解析异常时的回调,默认将抛出异常到服务器
        $socketConfig->setOnExceptionHandler(function ($server, $throwable, $raw, $client, Response $response) {
            $response->setMessage("服务器异常（客户端fd:{$client->getFd()}）");
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE); // 发送完主动关闭该连接
        });
        $dispatch = new Dispatcher($socketConfig);

        $subPort->on($register::onConnect, function (Server $server, int $fd, int $reactor_id) {
            echo "fd {$fd} connected";
        });

        $subPort->on($register::onReceive, function (Server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
            $dispatch->dispatch($server, $data, $fd, $reactor_id);
        });

        $subPort->on($register::onClose, function (Server $server, int $fd, int $reactor_id) {
            echo "fd {$fd} closed";
        });
    }
}
```

## socket 控制器响应对象

该对象是指 `\EasySwoole\Socket\Bean\Response` 类的对象。此响应类主要用于此调度结束或者调度出现异常，对连接后续的操作。

### 响应状态说明

正常响应(保持连接，服务端不主动关闭)(Response默认响应状态)

> \EasySwoole\Socket\Bean\Response::STATUS_OK;

响应后服务端主动关闭连接

> \EasySwoole\Socket\Bean\Response::STATUS_RESPONSE_AND_CLOSE;

服务端直接关闭连接

> \EasySwoole\Socket\Bean\Response::STATUS_CLOSE;

### 设置响应信息

响应信息会经过解析器 `Parser` 的 `encode` 方法处理。

```php
<?php
use EasySwoole\Socket\AbstractInterface\Controller;

class Test extends Controller
{
    public function testMessage()
    {   
        $this->response()->setMessage('test message');
    }
}
```

### 设置响应状态

当响应信息为空的时候，并不会发送信息给客户端。

#### 异常

可以自定义异常处理器，在服务 `Dispatcher` 异常时进行处理，使用示例如下：

注册自定义异常处理器：

```php
<?php

namespace EasySwoole\EasySwoole;

use App\Parser\TcpParser;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use Throwable;
use EasySwoole\Socket\Client\Tcp;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 注册 Dispatcher
        $config = new SocketConfig();
        $config->setType($config::TCP);
        $config->setParser(TcpParser::class);
        $dispatcher = new Dispatcher($config);
        
        ### 自定义异常处理器
        $config->setOnExceptionHandler(function (Server $server, Throwable $throwable, string $raw, Tcp $client, Response $response) {
            $response->setMessage('system error!');
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE);
        });
        $register->set($register::onReceive, function (Server $server, int $fd, int $reactorId, string $data) use ($dispatcher) {
            $dispatcher->dispatch($server, $data, $fd, $reactorId);
        });
    }
}
```

在控制器中模拟业务抛出异常：

```php
<?php
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {   
        throw new \Exception("invalid data");
    }
}
```

### 在 socket 控制器方法中使用响应对象

```php
<?php
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\AbstractInterface\Controller;

class Test extends Controller
{
    public function testStatus()
    {   
        $this->response()->setMessage('test status');
        $this->response()->setStatus(Response::STATUS_RESPONSE_OK);
    }
}
```

## 在 swoole 中使用组件

此组件可脱离 `EasySwoole` 主框架使用，方便开发者自行创建服务进行调度，属于 `socket` 事件调度器。

使用示例如下：

`server` ：

```php
<?php
declare(strict_types=1);

use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\Bean\Caller;
use Swoole\Server;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Socket\Dispatcher;

require_once __DIR__ . '/vendor/autoload.php';

class C extends Controller
{
    private   $hit     = 0;
    protected $hitTime = 0;

    public function __construct()
    {
        var_dump('controller create  ' . spl_object_hash($this));
        parent::__construct();
    }

    protected function onRequest(?string $actionName): bool
    {
        $this->hit++;
        $this->hitTime = time();
        return true;
    }

    public function test()
    {
        var_dump($this->hit, $this->hitTime);
        //        co::sleep(10);
        $this->response()->setMessage('time:' . time());
    }
}

class Parser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        $ret = new Caller();
        $ret->setControllerClass(C::class);
        $ret->setAction('test');
        return $ret;
    }

    /*
     * 如果这里返回null，则不给客户端任何数据
     */
    public function encode(Response $response, $client): ?string
    {
        return $response->__toString();
    }
}

$server = new Server('0.0.0.0', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
$server->set(['worker_num' => swoole_cpu_num()]);

$conf = new SocketConfig();
$conf->setType(SocketConfig::TCP);
$conf->setParser(new Parser());
$conf->setOnExceptionHandler(function (Server $server, \Throwable $throwable, string $raw, $client, Response $response) {
    $response->setStatus('error');
    $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE);
});

$dispatch = new Dispatcher($conf);

$server->on('connect', function ($server, $fd) use ($dispatch) {
    echo "Client: Connect. fd: {$fd}\n";
});

$server->on('receive', function ($server, $fd, $reactor_id, $data) use ($dispatch) {
    $dispatch->dispatch($server, $data, $fd, $reactor_id);
});

$server->on('close', function ($server, $fd) {
    echo "Client: Close. fd: {$fd}\n";
});

$server->start();
```

`client` ：

```php
<?php
declare(strict_types=1);

use Swoole\Coroutine\Client;
use function Swoole\Coroutine\run;

run(function () {
    $client = new Client(SWOOLE_TCP);
    if (!$client->connect('127.0.0.1', 9501)) {
        echo 'tcp connect fail!';
        return;
    }
    $client->send('test');
    $recvBody = $client->recv();
    var_dump($recvBody);
});
```
