---
title: easyswoole socket服务场景问题
meta:
  - name: description
    content: EasySwoole socket服务场景问题
---

# 常见问题

## 如何遍历全部链接

```php
use EasySwoole\EasySwoole\ServerManager;
$server = ServerManager::getInstance()->getSwooleServer();
$startFd = 0;

while (true) {
    $connectionList = $server->getClientList($startFd, 10);
    if ($connectionList === false || count($connectionList) === 0) {
        echo "finish\n";
        break;
    }

    $startFd = end($connectionList);
    var_dump($connectionList);

    foreach ($connectionList as $fd) {
        $server->send($fd, "broadcast");
    }
}
```

::: warning
具体可查看：[https://wiki.swoole.com/zh-cn/#/server/methods?id=getclientlist](https://wiki.swoole.com/zh-cn/#/server/methods?id=getclientlist)
:::

## 如何获取链接信息

```php
use EasySwoole\EasySwoole\ServerManager;
$server = ServerManager::getInstance()->getSwooleServer();
$clientInfo = $server->getClientInfo($fd);
```

::: warning
具体可查看：[https://wiki.swoole.com/zh-cn/#/server/methods?id=getclientinfo](https://wiki.swoole.com/zh-cn/#/server/methods?id=getclientinfo)
:::

## Socket有哪些开发场景?

- H5 即时游戏
- 网页聊天室
- 物联网开发
- 服务器 UDP 广播
- 车联网 
- 智能家居
- WEB 网页服务器
