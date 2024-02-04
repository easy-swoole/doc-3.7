---
title: easyswoole ORM 更新数据
meta:
  - name: description
    content: easyswoole ORM 更新
  - name: keywords
    content:  easyswoole ORM 更新
---

# 更新

> `update()` 方法，返回值为 `bool` 类型的值，值为 `true`时表示影响行数大于0的更新成功。

> `updateWithLimit()` 方法，返回值为 `int` 类型的值，值表示更新影响的行数。

> `fastUpdate` 方法，返回值为 `int` 类型的值，值表示更新影响的行数。

## 查找并更新

在取出数据后，更改字段内容后更新数据。

```php
<?php
$user = User::findRecord(1);
$user->name = 'easyswoole111';
$user->email = 'easyswoole111@qq.com';
$user->update();
```

## 直接更新数据

也可以直接带更新条件来更新数据

```php
$user = new User();
// updateWithLimit 方法第二个参数为更新条件
$user->updateWithLimit([
    'name'  => 'easyswoole112',
    'email' => 'easyswoole112@qq.com'
], ['id' => 1]);

// 调用静态方法
User::fastUpdate(['id' => 1], [
    'name'  => 'easyswoole112',
    'email' => 'easyswoole112@qq.com'
]);

User::fastUpdate(function (\EasySwoole\Mysqli\QueryBuilder $queryBuilder) {
  $queryBuilder->where('id', 1);
}, [
    'name'  => 'easyswoole112',
    'email' => 'easyswoole112@qq.com'
]);

User::fastUpdate(1, [
    'name'  => 'easyswoole112',
    'email' => 'easyswoole112@qq.com'
]);

User::fastUpdate('1,2', [
    'name'  => 'easyswoole112',
    'email' => 'easyswoole112@qq.com'
]);
```

必要的时候，你也可以使用 `Query` 对象来直接更新数据。

```php
<?php
$user = new User();
$user->queryLimit()->where('id', 1);
$user->updateWithLimit(['name' => 'easyswoole']);
```

## 闭包更新

可以通过闭包函数使用更复杂的更新条件，例如：

```php
<?php
$user = new User();
$user->updateWithLimit(['name' => 'easyswoole'], function (\EasySwoole\FastDb\Beans\Query $query) {
    // 更新status值为1 并且id大于10的数据
    $query->where('status', 1)->where('id', 10, '>');
}); // UPDATE `easyswoole_user` SET `name` = 'easyswoole' WHERE  `status` = 1  AND `id` > 10
```
