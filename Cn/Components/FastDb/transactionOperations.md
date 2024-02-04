---
title: easyswoole ORM 事务操作管理
meta:
  - name: description
    content: easyswoole ORM 事务操作管理
  - name: keywords
    content:  easyswoole ORM 事务操作管理
---

# 事务操作

使用事务处理的话，需要数据库引擎支持事务处理。比如 `MySQL` 的 `MyISAM` 不支持事务处理，需要使用 `InnoDB` 引擎。

手动控制事务逻辑，如：

```php
<?php
try {
    // 启动事务
    FastDb::getInstance()->begin();
    $user = User::findRecord(1000);
    $user->delete();
    // 提交事务
    FastDb::getInstance()->commit();
} catch (\Throwable $throwable) {
    // 回滚事务
    FastDb::getInstance()->rollback();
}

// 或者使用 `invoke` 方法
FastDb::getInstance()->invoke(function (\EasySwoole\FastDb\Mysql\Connection $connection) {
    try {
        // 启动事务
        FastDb::getInstance()->begin($connection);
        $user = User::findRecord(1000);
        $user->delete();
        // 提交事务
        FastDb::getInstance()->commit($connection);
    } catch (\Throwable $throwable) {
        // 回滚事务
        FastDb::getInstance()->rollback($connection);
    }

    return true;
});
```
