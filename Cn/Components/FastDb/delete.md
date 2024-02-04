---
title: easyswoole ORM 删除数据
meta:
  - name: description
    content: easyswoole ORM 删除数据
  - name: keywords
    content:  easyswoole ORM 删除数据
---

# 删除

> `delete()` 方法，返回值为 `bool` 类型的值，值为 `true`时表示影响行数大于0的删除成功。

> `fastDelete()` 方法返回值为 `int` 类型的值
> - 删除成功时返回值为 `int` 类型的值，表示删除操作影响的行数
> - 删除失败时返回值为 `null`

## 查找并删除

在取出数据后，然后删除数据。

```php
<?php
$user = User::findRecord(1);
$user->delete();
```

## 根据主键删除

直接调用静态方法

```php
User::fastDelete(1);
// 支持批量删除多个数据
User::fastDelete('1,2,3');
```

> 当 `fastDelete` 方法传入空值（包括空字符串和空数组）的时候不会做任何的数据删除操作，但传入0则是有效的。

## 条件删除

使用数组进行条件删除，例如：

```php
<?php
// 删除状态为0的数据
User::fastDelete(['status' => 0]);
```

还支持使用闭包删除，例如：

```php
<?php
User::fastDelete(function (\EasySwoole\Mysqli\QueryBuilder $query) {
    $query->where('id', 10, '>');
});
```
