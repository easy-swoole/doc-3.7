---
title: easyswoole ORM连接池连接预热
meta:
  - name: description
    content: easyswoole ORM连接池连接预热
  - name: keywords
    content: easyswoole orm|swoole orm|swoole协程orm|swoole协程mysql客户端|ORM连接池连接预热
---

# 连接预热

`FastDb::getInstance()->preConnect();` 方法用于预热连接池。

为了避免连接空档期突如其来的高并发，我们可以对数据库连接预热，也就是 `Worker` 进程启动的时候，提前准备好数据库连接。

对连接进行预热使用示例如下所示：

```php
<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\FastDb\FastDb;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        $mysqlArrayConfig = Config::getInstance()->getConf('MYSQL');
        $config = new \EasySwoole\FastDb\Config($mysqlArrayConfig);
        FastDb::getInstance()->addDb($config);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $register->add($register::onWorkerStart, function () {
            // 连接预热
            FastDb::getInstance()->preConnect();
        });
    }
}
```
