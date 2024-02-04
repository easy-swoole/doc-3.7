---
title: easyswoole ORM模型创建脚本
meta:
  - name: description
    content: easyswoole ORM模型创建脚本
  - name: keywords
    content: easyswoole orm|swoole orm|swoole协程orm|swoole协程mysql客户端
---

# 模型创建脚本

## 注册脚本命令

`ORM` 提供了创建模型的命令，您可以很方便的根据数据表创建对应模型。不过这个功能目前仅限在 `EasySwoole` 框架中使用。

```bash
php easyswoole.php model gen -table={table_name}
```

在使用脚本之前需要先在 `EasySwoole` 框架中进行注册 `ORM` 连接池和注册创建脚本命令，修改 `EasySwoole` 框架根目录的 `bootstrap.php` 文件，如下：

```php
<?php
// bootstrap.php
// 全局bootstrap事件
date_default_timezone_set('Asia/Shanghai');

$argvArr = $argv;
array_shift($argvArr);
$command = $argvArr[0] ?? null;
if ($command === 'model') {
    \EasySwoole\EasySwoole\Core::getInstance()->initialize();
}
\EasySwoole\Command\CommandManager::getInstance()->addCommand(new \EasySwoole\FastDb\Commands\ModelCommand());
```

## 创建模型

可选参数如下：

| 参数  | 类型 | 默认值 | 备注 |
| ------- | ------- | ------- | ------- |
| -db-connection | string | default | 连接池名称，脚本会根据当前连接池配置创建 |
| -path | string | App/Model | 模型路径 |
| -with-comments | bool | false | 是否增加字段属性注释 |

## 创建示例

在数据库中先导入数据表 `DDL`，如：

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

或数据库已有上述数据表也可。

执行如下命令，创建模型：

```bash
php easyswoole.php model gen -table=easyswoole_user -with-comments
```

创建的模型如下：

```php
<?php

declare(strict_types=1);

namespace App\Model;

use EasySwoole\FastDb\AbstractInterface\AbstractEntity;
use EasySwoole\FastDb\Attributes\Property;

/**
 * @property int $id
 * @property string|null $name
 * @property int|null $status
 * @property int|null $score
 * @property int|null $sex
 * @property string|null $address
 * @property string|null $email
 */
class EasyswooleUser extends AbstractEntity
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
