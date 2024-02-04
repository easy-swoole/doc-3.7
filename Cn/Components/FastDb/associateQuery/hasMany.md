---
title: easyswoole orm一对多关联查询
meta:
  - name: description
    content: easyswoole orm一对多关联查询
  - name: keywords
    content:  easyswoole orm一对多关联查询
---

# 一对多关联 hasMany

## 定义关联

```php
<?php
declare(strict_types=1);

namespace EasySwoole\FastDb\Tests\Model;

use EasySwoole\FastDb\AbstractInterface\AbstractEntity;
use EasySwoole\FastDb\Attributes\Property;
use EasySwoole\FastDb\Attributes\Relate;
use EasySwoole\FastDb\Tests\Model\UserCar;

/**
 * @property int    $id
 * @property string $name
 * @property int    $status
 * @property int    $score
 * @property string $email
 */
class User extends AbstractEntity
{
    #[Property(isPrimaryKey: true)]
    public int $id;
    #[Property]
    public ?string $name;
    #[Property]
    public ?int $status;
    #[Property]
    public ?int $score;
    #[Property]
    public ?int $create_time;
    #[Property]
    public ?string $info;
    #[Property]
    public ?string $foo;
    #[Property]
    public ?string $bar;
    #[Property]
    public ?int $login_time;
    #[Property]
    public ?int $login_times;
    #[Property]
    public ?int $read;
    #[Property]
    public ?string $title;
    #[Property]
    public ?string $content;
    #[Property]
    public ?string $email;

    public function tableName(): string
    {
        return 'easyswoole_user';
    }
    
    #[Relate(
        targetEntity: UserCar::class,
        targetProperty: 'user_id'
    )]
    public function cars()
    {
        return $this->relateMany();
    }
}
```

## 关联查询

```php
<?php
$article = User::findRecord(1);
// 获取用户拥有的所有车辆品牌
$listResult = $article->cars();
foreach ($listResult as $userCar) {
    echo $userCar->car_name . "\n";
}
// or
$objectArr = $listResult->toArray(); // 转换为 对象数组
foreach ($objectArr as $userCar) {
    echo $userCar->car_name . "\n";
}
```
