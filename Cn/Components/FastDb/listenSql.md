---
title: easyswoole ORM监听sql
meta:
  - name: description
    content: easyswoole ORM监听sql
  - name: keywords
    content: easyswoole orm|swoole orm|swoole协程orm|swoole协程mysql客户端|ORM监听sql
---

# 监听 sql

如果你想对数据库执行的任何 `SQL` 操作进行监听，可以在注册连接池时设置 `onQuery` 回调函数，使用如下方法：

```php
<?php
$config = new \EasySwoole\FastDb\Config();
$config->setHost('127.0.0.1');
$config->setUser('easyswoole');
$config->setPassword('');
$config->setDatabase('easyswoole');
$config->setName('default');
FastDb::getInstance()->addDb($config);


// 设置 onQuery 回调函数
FastDb::getInstance()->setOnQuery(function (\asySwoole\FastDb\Mysql\QueryResult $queryResult) {
   // 打印 sql
    if ($queryResult->getQueryBuilder()) {
        echo $queryResult->getQueryBuilder()->getLastQuery() . "\n";
    } else {
        echo $queryResult->getRawSql() . "\n";
    }
});
```
