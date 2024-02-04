---
title: easyswoole ORM 查询数据
meta:
  - name: description
    content: easyswoole ORM 查询数据
  - name: keywords
    content:  easyswoole ORM 查询数据
---

# 获取多个数据

> `findAll()` 方法返回的是一个包含模型对象的二维普通数组或者对象数组。返回的结果类型受参数 `returnAsArray` 的影响。

> `all()` 方法返回的是 `\EasySwoole\FastDb\Beans\ListResult` 类的对象。

```php
<?php
// 使用主键查询
$list = User::findAll('1,2');

// 使用数组查询
$list = User::findAll(['status' => 1]);

// 使用闭包查询
$list = User::findAll(function (\EasySwoole\Mysqli\QueryBuilder $query) {
    $query->where('status', 1)->limit(3)->orderBy('id', 'asc');
}, null, false);
foreach ($list as $key => $user) {
    echo $user->name;
}
```

> 数组方式和闭包方式的数据查询的区别在于，数组方式只能定义查询条件，闭包方式可以支持更多的连贯操作，包括排序、数量限制等。

```php
<?php
// 获取多个数据 不使用条件查询
/** @var User[] $users */
$users = (new User())->all(); // 返回结果：\EasySwoole\FastDb\Beans\ListResult 类的对象
foreach ($users as $user) {
    echo $user->name . "\n";
}

// 获取多个数据 使用条件查询
$userModel = new User();
$userModel->queryLimit()->where('id', [401, 403], 'IN')->where('name', 'easyswoole-1');
$users = $userModel->all(); // 返回结果：\EasySwoole\FastDb\Beans\ListResult 类的对象
foreach ($users as $user) {
    echo $user->name . "\n";
}
```
