---
title: easyswoole ORM模型定义
meta:
  - name: description
    content: easyswoole ORM模型定义
  - name: keywords
    content: easyswoole ORM模型定义
---

# FastDb 类使用

## 调用方法说明

### addDb

用于注册连接池。

```php
<?php
$config = new \EasySwoole\FastDb\Config([
    'name'              => 'default',    // 设置 连接池名称，默认为 default
    'host'              => '127.0.0.1',  // 设置 数据库 host
    'user'              => 'easyswoole', // 设置 数据库 用户名
    'password'          => 'easyswoole', // 设置 数据库 用户密码
    'database'          => 'easyswoole', // 设置 数据库库名
    'port'              => 3306,         // 设置 数据库 端口
    'timeout'           => 5,            // 设置 数据库连接超时时间
    'charset'           => 'utf8',       // 设置 数据库字符编码，默认为 utf8
    'autoPing'          => 5,            // 设置 自动 ping 客户端链接的间隔
    'useMysqli'         => false,        // 设置 不使用 php mysqli 扩展连接数据库
    // 配置 数据库 连接池配置，配置详细说明请看连接池组件 https://www.easyswoole.com/Components/Pool/introduction.html
    // 下面的参数可使用组件提供的默认值
    'intervalCheckTime' => 15 * 1000,    // 设置 连接池定时器执行频率
    'maxIdleTime'       => 10,           // 设置 连接池对象最大闲置时间 (秒)
    'maxObjectNum'      => 20,           // 设置 连接池最大数量
    'minObjectNum'      => 5,            // 设置 连接池最小数量
    'getObjectTimeout'  => 3.0,          // 设置 获取连接池的超时时间
    'loadAverageTime'   => 0.001,        // 设置 负载阈值
]);
// 或使用对象设置属性方式进行配置
// $config->setName('default');
// $config->setHost('127.0.0.1');
FastDb::getInstance()->addDb($config);
```

### testDb

用于测试连接池的数据库配置是否可用。

```php
FastDb::getInstance()->testDb();
FastDb::getInstance()->testDb('read');
FastDb::getInstance()->testDb('write');
```

### setOnQuery

设置连接池连接执行 `SQL` 查询时的回调，可用于监听 `SQL`，可查看监听 `SQL` 章节。

```php
<?php
FastDb::getInstance()->setOnQuery(function (\asySwoole\FastDb\Mysql\QueryResult $queryResult) {
    // 打印 sql
    if ($queryResult->getQueryBuilder()) {
        echo $queryResult->getQueryBuilder()->getLastQuery() . "\n";
    } else {
        echo $queryResult->getRawSql() . "\n";
    }
});
```

### invoke

可用于执行数据库操作。

在高并发情况下，资源浪费的占用时间越短越好，可以提高程序的服务效率。

`ORM` 默认情况下都是使用 `defer` 方法获取 `pool` 内的连接资源，并在协程退出时自动归还，在此情况下，在带来便利的同时，会造成不必要资源的浪费。

我们可以使用 `invoke` 方式，让 `ORM` 查询结束后马上归还资源，可以提高资源的利用率。

```php
<?php
$builder = new \EasySwoole\Mysqli\QueryBuilder();
$builder->raw('select * from user');
$result = FastDb::getInstance()->invoke(function (\EasySwoole\FastDb\Mysql\Connection $connection) use ($builder) {
    $connection->query($builder);
    return $connection->rawQuery("select * from user");
});
```

### begin

启动事务。

```php
FastDb::getInstance()->begin();
```

### commit

提交事务。

```php
FastDb::getInstance()->commit();
```

### rollback

回滚事务。

```php
FastDb::getInstance()->rollback();
```

### query

自定义 `SQL` 执行。

```php
$builder = new \EasySwoole\Mysqli\QueryBuilder();
$builder->raw("select * from user where id = ?", [1]);
FastDb::getInstance()->query($builder);
```

> 原生 `SQL` 表达式将会被当做字符串注入到查询中，因此你应该小心使用，避免创建 `SQL` 注入的漏洞。

### rawQuery

自定义 `SQL` 执行。

```php
FastDb::getInstance()->rawQuery('select * from user where id = 1');
```

> 原生 `SQL` 表达式将会被当做字符串注入到查询中，因此你应该小心使用，避免创建 `SQL` 注入的漏洞。

### currentConnection

获取当前所用的连接。

```php
FastDb::getInstance()->currentConnection();
```

### reset

销毁所有连接池。

```php
FastDb::getInstance()->reset();
```

### preConnect

用于预热连接池。

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

### isInTransaction

当前连接是否处于事务中。

```php
FastDb::getInstance()->isInTransaction();
```

### getConfig

根据连接池名称获取当前连接池配置。

```php
FastDb::getInstance()->getConfig();
FastDb::getInstance()->getConfig('read');
```
