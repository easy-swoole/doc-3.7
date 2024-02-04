---
title: easyswoole ORM模型定义
meta:
  - name: description
    content: easyswoole ORM模型定义
  - name: keywords
    content: easyswoole ORM模型定义
---

# Model

## 定义模型

### 模型定义规范

1. 任何模型都必须继承 `\EasySwoole\FastDb\AbstractInterface\AbstractEntity` 并实现 `tableName()` 方法，该方法用于返回该数据表的表名。

2. 任何模型都必须具有一个唯一主键，作为某个模型对象的唯一id，一般建议为 `int` 类型的自增id。

3. 对象的属性，也就是数据表对应的字段，请用 `#[Property]` 进行标记。

### 示例

例如，我们有个表名为 `user` 的数据表，表结构如下：

```sql
CREATE TABLE `easyswoole_user`
(
    `id`      int unsigned NOT NULL AUTO_INCREMENT COMMENT 'increment id',
    `name`    varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'name',
    `status`  tinyint unsigned DEFAULT '0' COMMENT 'status',
    `score`   int unsigned DEFAULT '0' COMMENT 'score',
    `sex`     tinyint unsigned DEFAULT '0' COMMENT 'sex',
    `address` json                                                          DEFAULT NULL COMMENT 'address',
    `email`   varchar(150) COLLATE utf8mb4_general_ci                       DEFAULT NULL COMMENT 'email',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

则它对应的实体类如下：

```php
<?php
declare(strict_types=1);

namespace App\Model;

use EasySwoole\FastDb\AbstractInterface\AbstractEntity;
use EasySwoole\FastDb\Attributes\Property;

/**
 * @property int $id increment id
 * @property string|null $name name
 * @property int|null $status status
 * @property int|null $score score
 * @property int|null $sex sex
 * @property string|null $address address
 * @property string|null $email email
 */
class EasySwooleUser extends AbstractEntity
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
    public ?int $sex;
    #[Property]
    public ?string $address;
    #[Property]
    public ?string $email;

    public function tableName(): string
    {
        return 'easyswoole_user';
    }
}
```
