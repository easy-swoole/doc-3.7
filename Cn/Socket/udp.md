---
title: easyswoole udp服务
meta:
  - name: description
    content: easyswoole udp服务
  - name: keywords
    content: easyswoole udp服务|swoole 硬件|swoole iot
---

# UDP 服务

`UDP` 为应用程序提供了一种无需建立连接就可以发送封装的 `IP` 数据包的方法。

在 `EasySwoole` 中使用 `UDP` 服务有2种方法：

## 主服务

可以将 `UDP` 服务作为 `EasySwoole` 的主服务

首先修改配置文件中 `MAIN_SERVER.SERVER_TYPE` 配置项为 `EASYSWOOLE_SERVER`，`SOCK_TYPE` 配置项修改为 `SWOOLE_UDP`。

然后在 `EasySwooleEvent` 的 [mainServerCreate](/FrameDesign/event.html#mainServerCreate) 事件中注册回调，注册参考示例如下：

```php
<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use Swoole\Server as SwooleServer;

class EasySwooleEvent implements Event
{
    // ...

    public static function mainServerCreate(EventRegister $register)
    {
        // ...

        $server = ServerManager::getInstance()->getSwooleServer();
        $subPort = $server->addlistener('0.0.0.0', 9502, SWOOLE_UDP);

        $subPort->on($register::onPacket, function (SwooleServer $server, string $data, array $clientInfo) {
            $server->sendto($clientInfo['address'], $clientInfo['port'], 'Server: ' . $data);
        });
    }
}
```

## 子服务

可以将 `UDP` 服务作为 EasySwoole 的子服务。顾名思义：另外开一个端口进行 `UDP` 监听。

在 `EasySwooleEvent` 中 [mainServerCreate](/FrameDesign/event.html#mainServerCreate) 事件，进行子服务监听，参考代码如下：

```php
public static function mainServerCreate(\EasySwoole\EasySwoole\Swoole\EventRegister $register)
{
    $server = \EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer();

    $subPort = $server->addlistener('0.0.0.0', 9503, SWOOLE_UDP);
    $subPort->on($register::onPacket, function (\Swoole\Server $server, string $data, array $clientInfo) {
           $server->sendto($clientInfo['address'], $clientInfo['port'], 'Server：' . $data);
    });
}
```





