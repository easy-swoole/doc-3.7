---
title: easyswoole\websocket服务 meta:

- name: description content: easyswoole\websocket服务
- name: keywords content: easyswoole websocket服务|swoole websocket|swoole即时通讯|swoole聊天室|php websocket|php聊天室

---

# WebSocket 服务

`WebSocket` 是一种在单个 `TCP` 连接上进行全双工通信的协议。`WebSocket` 使得客户端和服务器之间的数据交换变得更加简单，允许服务端主动向客户端推送数据。在`WebSocket`
中，浏览器和服务器只需要完成一次握手，两者之间就直接可以创建持久性的连接，并进行双向数据传输。

## 主服务

可以将 `WebSocket` 服务作为 `EasySwoole` 的主服务。

首先修改配置文件 `MAIN_SERVER.SERVER_TYPE` 配置项为 `EASYSWOOLE_WEB_SOCKET_SERVER`。

然后在 `EasySwooleEvent` 中的 [mainServerCreate](/FrameDesign/event.html#mainServerCreate) 事件注册回调，参考示例如下：

```php
<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\WebSocket\Frame;
use Swoole\Server as SwooleServer;

class EasySwooleEvent implements Event
{
    // ...

    public static function mainServerCreate(EventRegister $register)
    {
        $register->set($register::onOpen, function (SwooleWebSocketServer $server, SwooleHttpRequest $request) {
            var_dump($request->fd, $request->server);
            $server->push($request->fd, "hello, welcome\n");
        });

        $register->set($register::onMessage, function (SwooleWebSocketServer $server, Frame $frame) {
            echo "Message: {$frame->data}\n";
            $server->push($frame->fd, "server: {$frame->data}");
        });

        $register->set($register::onClose, function (SwooleServer $server, int $fd) {
            echo "client-{$fd} is closed\n";
        });
    }
}
```


## 子服务

可以将 `WebSocket` 服务作为 `EasySwoole` 的子服务

如果想要将 `WebSocket` 作为 EasySwoole 的子服务，则主服务必须也为 `WebSocket` 服务类型。

然后开另外一个端口进行 `WebSocket` 监听。

在 `EasySwooleEvent` 中的 [mainServerCreate](/FrameDesign/event.html#mainServerCreate) 事件中进行子服务监听，参考代码如下：

```php
<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\WebSocket\Frame;
use Swoole\Server as SwooleServer;

class EasySwooleEvent implements Event
{
    // ...

    public static function mainServerCreate(EventRegister $register)
    {
        // ....

        $server = ServerManager::getInstance()->getSwooleServer();
        $subPort = $server->addlistener('0.0.0.0', 9502, SWOOLE_TCP);

        $subPort->set(['open_websocket_protocol' => true]);

        $subPort->on($register::onOpen, function (SwooleWebSocketServer $server, SwooleHttpRequest $request) {
            var_dump($request->fd, $request->server);
            $server->push($request->fd, "hello, welcome\n");
        });

        $subPort->on($register::onMessage, function (SwooleWebSocketServer $server, Frame $frame) {
            echo "Message: {$frame->data}\n";
            $server->push($frame->fd, "server: {$frame->data}");
        });

        $subPort->on($register::onClose, function (SwooleServer $server, int $fd) {
            echo "client-{$fd} is closed\n";
        });
    }
}
```
