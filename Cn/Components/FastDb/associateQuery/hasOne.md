---
title: easyswoole orm一对一关联查询
meta:
  - name: description
    content: easyswoole orm一对一关联查询
  - name: keywords
    content:  easyswoole orm一对一关联查询
---

# 一对一关联 hasOne

## 定义关联

定义一对一关联，例如，每个用户都有一个个人资料信息，我们定义 `User` 模型如下：

```php
<?php
declare(strict_types=1);

namespace EasySwoole\FastDb\Tests\Model;

use EasySwoole\FastDb\AbstractInterface\AbstractEntity;
use EasySwoole\FastDb\Attributes\Property;
use EasySwoole\FastDb\Attributes\Relate;
use EasySwoole\FastDb\Tests\Model\UserProfile;

/**
 * @property int    $id
 * @property string $name
 * @property string $email
 */
class User extends AbstractEntity
{
    #[Property(isPrimaryKey: true)]
    public int $id;
    #[Property]
    public ?string $name;
    #[Property]
    public ?string $email;

    public function tableName(): string
    {
        return 'easyswoole_user';
    }

    #[Relate(
        targetEntity: UserProfile::class,
        targetProperty: 'user_id' // 关联模型的数据表的主键
    )]
    public function profile()
    {
        return $this->relateOne();
    }
}
```

## 关联查询

定义好关联之后，就可以使用下面的方法获取关联数据：

```php
<?php
$user = User::findRecord(1);
// 输出 UserProfile 关联模型的email属性
echo $user->profile()->email;
```
