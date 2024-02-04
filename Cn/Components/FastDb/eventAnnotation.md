---
title: easyswoole ORM事件注解
meta:
  - name: description
    content: easyswoole ORM事件注解
  - name: keywords
    content: easyswoole ORM事件注解
---

# 事件注解

## 适用场景

模型事件类似于 `ThinkPHP` 框架模型的模型事件，可用于在数据写入数据库之前做一些预处理操作。

模型事件是指在进行模型的写入操作的时候触发的操作行为，包括调用模型对象的 `insert`、`delete`、`update` 方法以及对实体对象初始化时触发。

模型类支持 `OnInitialize`、`OnInsert`、`OnDelete`、`OnUpdate` 事件。

| 事件行为注解  | 描述                                     |
| ------- | ------------------------------------------ |
| OnInitialize | 实体被实例化时触发 |
| OnInsert     | 新增前 |
| OnDelete     | 删除前 |
| OnUpdate     | 更新前 |

## 使用示例

### 声明事件注解

在模型类中可以通过注解及定义类方法来实现事件注解的声明，如下所示：

```php
<?php
declare(strict_types=1);

namespace EasySwoole\FastDb\Tests\Model;

use EasySwoole\FastDb\AbstractInterface\AbstractEntity;
use EasySwoole\FastDb\Attributes\Hook\OnInitialize;
use EasySwoole\FastDb\Attributes\Hook\OnInsert;
use EasySwoole\FastDb\Attributes\Hook\OnDelete;
use EasySwoole\FastDb\Attributes\Hook\OnUpdate;
// ...

/**
 * @property int    $id
 * @property string $name
 * @property int    $status
 * @property int    $score
 * @property int    $create_time
 */
#[OnInitialize('onInitialize')]
#[OnInsert('onInsert')]
#[OnDelete('onDelete')]
#[OnUpdate('onUpdate')]
class User extends AbstractEntity
{
    // ...
    
    public function onInitialize()
    {
        // todo::
    }

    public function onInsert()
    {
        if (empty($this->status)) {
            return false;
        }
        if (empty($this->create_time)) {
            $this->create_time = time();
        }
    }

    public function onDelete()
    {
        // todo::
    }

    public function onUpdate()
    {
        // todo::
    }
}
```

上面定义了 `OnInitialize`、`OnInsert`、`OnDelete`、`OnUpdate` 事件注解，并在注解中通过形如 `#[OnInitialize('onInitialize')]`
的方式给 `OnInitialize` 注解传入参数，给对应的事件行为设置事件被触发时执行的回调 `onInitialize`、`onInsert`
、`onDelete`、`onUpdate`。

设置的回调方法会自动传入一个参数（当前的模型对象实例），并且 `OnInsert`、`OnDelete`、`OnUpdate` 事件的回调方法(`onInsert`
、`onDelete`、`onUpdate`) 如果返回 `false`，则不会继续执行。

### 使用

```php
$user = new User(['name' => 'EasySwoole', 'id' => 1000]);
$result = $user->insert();
var_dump($result); // false，返回 false，表示 insert 失败。
```
