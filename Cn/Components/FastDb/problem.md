---
title: easyswoole ORM 常见问题
meta:
  - name: description
    content: easyswoole ORM 常见问题
  - name: keywords
    content:  easyswoole ORM 常见问题
---

# 常见问题

## 1.Method Swoole\Coroutine\MySQL::__construct() is deprecated

如果在运行过程中出现类似 `PHP Deprecated: Method Swoole\Coroutine\MySQL::__construct() is deprecated in /demo/vendor/easyswoole/mysqli/src/Client.php on line 160` 这样的警告，请修改连接池注册时使用的配置中的 `useMysqli` 为 `true` 选项，即可解决这个告警。
