---
title: easyswoole/tcp服务器
meta:
  - name: description
    content: easyswoole/tcp服务器
  - name: keywords
    content: easyswoole tcp服务器|swoole tcp
---

# TCP 服务

## TCP 基础 Demo

`EasySwoole` 创建 `TCP` 服务器，有两种以下方式：

> 1.将 TCP 服务作为 EasySwoole 的主服务。

首先修改配置文件中 `MAIN_SERVER.SERVER_TYPE` 配置项为 `EASYSWOOLE_SERVER`。

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
        $register->add($register::onConnect, function (SwooleServer $server, int $fd, int $reactorId) {
            echo "fd{$fd} connected\n";
        });

        $register->add($register::onReceive, function (SwooleServer $server, int $fd, int $reactorId, string $data) {
            echo "fd:{$fd} receive_data:{$data}\n";
        });

        $register->add($register::onClose, function (SwooleServer $server, int $fd, int $reactorId) {
            echo "fd {$fd} closed\n";
        });
    }
}
```

> 2.将 TCP 服务作为 EasySwoole 的子服务。顾名思义：另外开一个端口进行 `tcp` 监听。

在 `EasySwooleEvent` 中的 [mainServerCreate](/FrameDesign/event.html#mainServerCreate) 事件中进行子服务监听，参考代码如下：

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
        // ....

        $server = ServerManager::getInstance()->getSwooleServer();
        $subPort = $server->addlistener('0.0.0.0', 9502, SWOOLE_TCP);

        $subPort->set([
            // swoole 相关配置
            'open_length_check' => false,
        ]);

        $subPort->on($register::onConnect, function (SwooleServer $server, int $fd, int $reactorId) {
            echo "fd {$fd} connected\n";
        });

        $subPort->on($register::onReceive, function (SwooleServer $server, int $fd, int $reactorId, string $data) {
            echo "fd:{$fd} received_data:{$data}\n";
        });

        $subPort->on($register::onClose, function (SwooleServer $server, int $fd, int $reactorId) {
            echo "fd {$fd} closed\n";
        });
    }
}
```


## 如何处理粘包

> 1.解决思路

- 方法1：通过标识 `EOF`，例如 `http` 协议，通过 `\r\n\r\n` 的方式去表示该数据已经完结，我们可以自定义一个协议。例如当接收到 "结尾666" 字符串时，代表该字符串已经结束，如果没有获取到，则存入缓冲区，等待结尾字符串，或者如果获取到多条，则通过该字符串剪切出其他数据。
  
- 方法2：定义消息头，通过特定长度的消息头进行获取。例如我们定义一个协议，前面 10 位字符串都代表着之后数据主体的长度，那么我们传输数据时，只需要 `000000000512346` (前10位为协议头，表示了这条数据的大小，后面的为数据)，每次我们读取只先读取10位，获取到消息长度，再读取消息长度那么多的数据，这样就可以保证数据的完整性了。(但是为了不被混淆，协议头也得像 `EOF` 一样标识)
  
- 方法3：通过 `pack` 二进制处理，相当于于方法2，将数据通过二进制封装拼接进消息中，通过验证二进制数据去读取信息，`swoole` 采用的就是这种方式。

:::warning
可查看 swoole 官方文档: [https://wiki.swoole.com/zh-cn/#/learn?id=tcp数据包边界问题](https://wiki.swoole.com/zh-cn/#/learn?id=tcp数据包边界问题)
:::
