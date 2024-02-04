---
title: ORM数组访问和转换
meta:
  - name: description
    content: Easyswoole ORM数组访问和转换
  - name: keywords
    content:  swoole|swoole 拓展|swoole 框架|EasySwoole mysql ORM|EasySwoole ORM|Swoole mysqli协程客户端|swoole ORM|查询|ORM结果数组访问和转换
---

# 数组访问和转换

转换为数组

可以使用 `toArray` 方法将当前的模型实例输出为数组，例如：

```php
<?php
$user = User::findRecord(1);
var_dump($user->toArray(false));

/** @var \EasySwoole\FastDb\Beans\ListResult $listResult */
$listResult = (new User)->all();
$objectArr = $listResult->toArray(); // 转换为 对象数组
```
