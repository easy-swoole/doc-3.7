---
title: easyswoole orm 插入数据
meta:
  - name: description
    content: easyswoole orm 插入数据
  - name: keywords
    content:  easyswoole orm 插入数据
---

# 新增

## 添加一条数据

> `insert()` 方法，返回值为 `bool` 类型的值，返回值为 `true` 表示添加成功，返回值为 `false` 表示添加失败。

第一种是实例化模型对象后赋值并保存：

```php
<?php
$user = new User();
$user->name = 'easyswoole';
$user->email = 'easyswoole@qq.com';
$user->insert();
// 相当于 sql: INSERT  INTO `easyswoole_user` (`name`, `email`)  VALUES ('easyswoole', 'easyswoole@qq.com')
```

也可以使用 `setData` 方法批量赋值：

```php
<?php
$user = new User();
$user->setData([
    'name'  => 'easyswoole',
    'email' => 'easyswoole@qq.com'
]);
$user->insert();
```

或者直接在实例化的时候传入数据

```php
<?php
$user = new User([
    'name'  => 'easyswoole',
    'email' => 'easyswoole@qq.com'
]);
$user->insert();
```

## 获取自增ID

如果要获取新增数据的自增ID，可以使用下面的方式：

```php
<?php
$user = new User();
$user->name = 'easyswoole';
$user->email = 'easyswoole@qq.com';
$user->insert();
// 获取自增ID
echo $user->id;
```

注意这里其实是获取模型的主键，如果你的主键不是 `id`，而是 `user_id` 的话，其实获取自增ID就变成这样：

```php
<?php
$user = new User();
$user->name = 'easyswoole';
$user->email = 'easyswoole@qq.com';
$user->insert();
// 获取自增ID
echo $user->user_id;
```

## 添加多条数据

> `insertAll()` 方法新增数据返回的是包含新增模型（带自增ID）的对象数组 或 普通数组。

> `insertAll()` 方法的返回类型受模型的 `queryLimit` 属性 的 `fields` 属性的 `returnAsArray` 属性影响（可能返回普通数组）。

支持批量新增，可以使用：

```php
<?php
$user = new User();
$list = [
    ['name' => 'easyswoole-1', 'email' => 'easyswoole1@qq.com'],
    ['name' => 'easyswoole-2', 'email' => 'easyswoole2@qq.com']
];
$user->insertAll($list); // 结果为 对象数组

$user = new User();
$list = [
    ['name' => 'easyswoole-1', 'email' => 'easyswoole1@qq.com'],
    ['name' => 'easyswoole-2', 'email' => 'easyswoole2@qq.com']
];
$user->queryLimit()->fields(null, true);
$user->insertAll($list); // 结果为 普通数组
```

`insertAll` 方法新增数据默认会自动识别数据是需要新增还是更新操作，当数据中存在主键的时候会认为是更新操作，如果你需要带主键数据批量新增，可以使用下面的方式：

```php
<?php
$user = new User;
$list = [
    ['id' => 1, 'name' => 'easyswoole-1', 'email' => 'easyswoole1@qq.com'],
    ['id' => 2, 'name' => 'easyswoole-2', 'email' => 'easyswoole2@qq.com']
];
$user->insertAll($list, false);
```

## onInsert注解

修改 `User` 模型类文件，添加 `OnInsert` 注解 和 `onInsert` 方法，`onInsert` 方法用于对添加前的数据做一些处理。

User.php

```php
<?php

declare(strict_types=1);

namespace App\Model;

use EasySwoole\FastDb\AbstractInterface\AbstractEntity;
use EasySwoole\FastDb\Attributes\Hook\OnInsert;
// ...

/**
 * @property int    $id
 * @property string $name
 * @property int    $status
 * @property int    $create_time
 * @property string $email
 */
#[OnInsert('onInsert')]
class User extends AbstractEntity
{
    // ...
    
    public function onInsert()
    {
        if (empty($this->create_time)) {
            $this->create_time = time();
        }
        if (empty($this->status)) {
            $this->status = 1;
        }
    }
}
```

然后尝试新增数据

```php
<?php
$user = new User();
$user->name = 'easyswoole';
$user->email = 'easyswoole@qq.com';
$user->insert(); // INSERT  INTO `easyswoole_user` (`name`, `status`, `create_time`, `email`)  VALUES ('easyswoole', 1, 1704521166, 'easyswoole@qq.com')
```

## ON DUPLICATE KEY UPDATE

```php
<?php
$user = new User();
$user->name = 'easyswoole100';
$updateDuplicateCols = ['name'];
$user->insert($updateDuplicateCols); // INSERT  INTO `easyswoole_user` (`name`, `status`, `create_time`)  VALUES ('easyswoole100', 1, 1704521621) ON DUPLICATE KEY UPDATE `name` = 'easyswoole100'

$user = new User();
$user->name = 'easyswoole100';
$updateDuplicateCols = ['name', 'id' => 1];
$user->insert($updateDuplicateCols); // INSERT  INTO `easyswoole_user` (`name`, `status`, `create_time`)  VALUES ('easyswoole100', 1, 1704521622) ON DUPLICATE KEY UPDATE `id` = 1, `name` = 'easyswoole100'
```
