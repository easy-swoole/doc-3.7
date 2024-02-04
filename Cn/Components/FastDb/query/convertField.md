---
title: ORM结果转换字段
meta:
  - name: description
    content: Easyswoole ORM组件,
  - name: keywords
    content:  swoole|swoole 拓展|swoole 框架|EasySwoole mysql ORM|EasySwoole ORM|Swoole mysqli协程客户端|swoole ORM|查询|ORM结果转换字段
---

# 转换字段

例如我们有数据表 `student_info`，`DDL` 如下：

```php
CREATE TABLE `student_info` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `studentId` int unsigned NOT NULL DEFAULT '0' COMMENT 'student id',
  `address` json DEFAULT NULL COMMENT 'address',
  `note` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'note',
  `sex` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'sex:1=male 2=female 0=unknown',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

我们可以对 `address` 和 `sex` 字段做转换处理来满足业务开发需求，这里我们用到了 `php8` 的枚举特性。

定义为模型为：

```php
<?php
namespace EasySwoole\FastDb\Tests\Model;

use EasySwoole\FastDb\AbstractInterface\AbstractEntity;
use EasySwoole\FastDb\Attributes\Property;
use EasySwoole\FastDb\Tests\Model\Address;
use EasySwoole\FastDb\Tests\Model\SexEnum;

class StudentInfoModel extends AbstractEntity
{
    #[Property(isPrimaryKey: true)]
    public int $id;

    #[Property()]
    public int $studentId;

    #[Property(
        convertObject: Address::class
    )]
    public Address $address;

    #[Property]
    public ?string $note;

    #[Property(
        convertObject: SexEnum::class
    )]
    public SexEnum $sex;

    function tableName(): string
    {
        return "student_info";
    }
}
```

Address.php

```php
<?php
namespace EasySwoole\FastDb\Tests\Model;

use EasySwoole\FastDb\AbstractInterface\ConvertJson;

class Address extends ConvertJson
{
    public $city;
    public $province;
}
```

SexEnum.php 使用枚举特性。

```php
<?php
namespace EasySwoole\FastDb\Tests\Model;

use EasySwoole\FastDb\AbstractInterface\ConvertObjectInterface;

enum SexEnum implements ConvertObjectInterface
{
    case UNKNUWN;
    case MALE;
    case FEMAILE;

    public static function toObject(mixed $data): object
    {
        switch ($data){
            case 1:{
                return self::MALE;
            }
            case 2:{
                return self::FEMAILE;
            }
            default:{
                return self::UNKNUWN;
            }
        }
    }

    function toValue()
    {
        return match ($this){
            self::MALE=>1,
            self::FEMAILE=>2,
            default=>0
        };
    }
}
```

转换字段使用示例：

```php
<?php
// 添加记录
$address = new \EasySwoole\FastDb\Tests\Model\Address();
$address->province = 'FuJian';
$address->city = 'XiaMen';
$sex = \EasySwoole\FastDb\Tests\Model\SexEnum::MALE;
$model = new StudentInfoModel([
    'studentId' => 1,
    'address'   => $address->toValue(),
    'sex'       => $sex->toValue(),
    'note'      => 'this is note',
]);
// or
// $model->address = $address;
// $model->sex = $sex;
$model->insert(); // INSERT  INTO `student_info` (`studentId`, `address`, `note`, `sex`)  VALUES (1, '{\"city\":\"XiaMen\",\"province\":\"FuJian\"}', 'this is note', 1)

// 查询一条数据
$studentInfo = StudentInfoModel::findRecord(1);
var_dump($studentInfo->address->city); // "XiaMen"
var_dump($studentInfo->address->province); // "FuJian"
var_dump($studentInfo->sex); // 枚举类型 enum(EasySwoole\FastDb\Tests\Model\SexEnum::MALE)
var_dump($studentInfo->toArray(false));

// 查询多条数据
$studentInfo = new StudentInfoModel();
$studentInfos = $studentInfo->all();
foreach ($studentInfos as $studentInfo) {
    var_dump($studentInfo->address->city);
    var_dump($studentInfo->address->province);
    var_dump($studentInfo->sex);
    var_dump($studentInfo->toArray(false));
}
```
