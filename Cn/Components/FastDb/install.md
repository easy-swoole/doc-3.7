---
title: easyswoole ORM安装
meta:
  - name: description
    content: easyswoole ORM安装
  - name: keywords
    content: easyswoole orm|swoole orm|swoole协程orm|swoole协程mysql客户端
---

# ORM

`EasySwoole` 为了支持以 `PHP 8` 注解的方式来定义数据库对象映射，于是开发了 `fast-db` 这个数据库操作组件。

::: tip
   关于旧版本 `ORM` 文档的用法可查看 [Github](https://github.com/easy-swoole/doc/tree/master/Cn/Components/Orm) 或 [Gitee](https://gitee.com/1592328848/easyswoole-old-doc/tree/master/Cn/Components/Orm)。
:::

## 组件要求

- EasySwoole >=3.7.1
- php: >= 8.1
- easyswoole/mysqli: ^3.0
- easyswoole/pool: ^2.0
- easyswoole/spl: ^2.0

## 安装

> composer require easyswoole/fast-db

## 连接池注册

### 在 EasySwoole 中使用

首先我们在 `EasySwoole` 框架的 `EasySwooleEvent` 事件（即框架根目录的 `EasySwooleEvent.php` 文件中）的 `initialize` 方法 或 `mainServerCreate` 方法中进行注册连接，如下所示：

EasySwooleEvent.php

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

        // 注册方式1：在 initialize 方法中注册连接
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
        // 或在注册时指定连接池的名称
        // FastDb::getInstance()->addDb($config, $config['name']);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 注册方式2：在 mainServerCreate 方法中注册连接
//        $config = new \EasySwoole\FastDb\Config([
//            'name'              => 'default',    // 设置 连接池名称，默认为 default
//            'host'              => '127.0.0.1',  // 设置 数据库 host
//            'user'              => 'easyswoole', // 设置 数据库 用户名
//            'password'          => 'easyswoole', // 设置 数据库 用户密码
//            'database'          => 'easyswoole', // 设置 数据库库名
//            'port'              => 3306,         // 设置 数据库 端口
//            'timeout'           => 5,            // 设置 数据库连接超时时间
//            'charset'           => 'utf8',       // 设置 数据库字符编码，默认为 utf8
//            'autoPing'          => 5,            // 设置 自动 ping 客户端链接的间隔
//            // 配置 数据库 连接池配置，配置详细说明请看连接池组件 https://www.easyswoole.com/Components/Pool/introduction.html
//            // 下面的参数可使用组件提供的默认值
//            'intervalCheckTime' => 15 * 1000,    // 设置 连接池定时器执行频率
//            'maxIdleTime'       => 10,           // 设置 连接池对象最大闲置时间 (秒)
//            'maxObjectNum'      => 20,           // 设置 连接池最大数量
//            'minObjectNum'      => 5,            // 设置 连接池最小数量
//            'getObjectTimeout'  => 3.0,          // 设置 获取连接池的超时时间
//            'loadAverageTime'   => 0.001,        // 设置 负载阈值
//        ]);
//        FastDb::getInstance()->addDb($config);
    }
}
```

> 上述2种注册方式注册结果是一样的。如需注册多个链接，请在配置项中加入 name 属性用于区分连接池。

### 在其他框架中使用

```php
<?php
use EasySwoole\FastDb\FastDb;
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
FastDb::getInstance()->addDb($config);
```

### 配置项解析

`\EasySwoole\FastDb\Config` 继承自 `\EasySwoole\Pool\Config` ，因此 `ORM` 具备连接池的特性。

- autoPing
- intervalCheckTime
- maxIdleTime
- maxObjectNum
- minObjectNum
