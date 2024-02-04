---
title: easyswoole ORM 查询数据
meta:
  - name: description
    content: easyswoole ORM 查询数据
  - name: keywords
    content:  easyswoole ORM 查询数据
---

# 查询单个数据

> `findRecord()` 方法，返回值为当前模型的对象实例，可以使用模型的方法。

> `find()` 方法，返回值为当前模型的对象实例，可以使用模型的方法。

获取单个数据的方法包括：

```php
<?php
// 取出主键为1的数据
$user = User::findRecord(1);
echo $user->name;

// 使用数组查询
$user = User::findRecord(['name' => 'easyswoole']);
echo $user->name;

// 使用闭包查询
$user = User::findRecord(function (\EasySwoole\Mysqli\QueryBuilder $query) {
    $query->where('name', 'easyswoole');
});
echo $user->name;
```

或者在实例化模型后调用查询方法

```php
$user = new User();
// 查询单个数据
$user->queryLimit()->where('name', 'easyswoole');
$userModel = $user->find();
echo $userModel->name;
```
