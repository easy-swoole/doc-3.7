---
title: easyswoole orm 聚合操作 快捷查询
meta:
  - name: description
    content: easyswoole orm 聚合操作 快捷查询
  - name: keywords
    content:  easyswoole orm 聚合操作 快捷查询
---

# 聚合

在模型中也可以调用数据库的聚合方法进行查询，例如：

| 方法  | 说明                                     |
| ------- | ------------------------------------------ |
| count | 统计数量，参数是要统计的字段名（可选）   |
| max   | 获取最大值，参数是要统计的字段名（必须） |
| min   | 获取最小值，参数是要统计的字段名（必须） |
| avg   | 获取平均值，参数是要统计的字段名（必须） |
| sum   | 获取总分，参数是要统计的字段名（必须）   |

## count

```php
<?php
$user = new User();
$user->count();
// SELECT  COUNT(*) as count FROM `easyswoole_user` LIMIT 1

$user->count('id', 'name');
// SELECT  COUNT(id) as count FROM `easyswoole_user` GROUP BY name  LIMIT 1

$user->queryLimit()->fields(['id', 'name']);
$user->count(null, 'name');
// SELECT  COUNT(`id`) as id, COUNT(`name`) as name FROM `easyswoole_user` GROUP BY name  LIMIT 1
```

## max

```php
$user = new User();
$user->max('id');
// SELECT  MAX(`id`) as id FROM `easyswoole_user` LIMIT 1

$user->max('id', 'name');
// SELECT  MAX(`id`) as id FROM `easyswoole_user` GROUP BY name  LIMIT 1

$user->max(['id', 'name'], 'name');
// SELECT  MAX(`id`) as id , MAX(`name`) as name FROM `easyswoole_user` GROUP BY name  LIMIT 1
```

## min

```php
<?php
$user = new User();
$user->min('id');
// SELECT  MIN(`id`) as id FROM `easyswoole_user` LIMIT 1

$user->min('id', 'name');
// SELECT  MIN(`id`) as id FROM `easyswoole_user` GROUP BY name  LIMIT 1

$user->min(['id', 'name'], 'name');
// SELECT  MIN(`id`) as id , MIN(`name`) as name FROM `easyswoole_user` GROUP BY name  LIMIT 1
```

## avg

```php
<?php
$user = new User();
$user->avg('id');
// SELECT  AVG(`id`) as id FROM `easyswoole_user` LIMIT 1

$user->avg('id', 'name');
// SELECT  AVG(`id`) as id FROM `easyswoole_user` GROUP BY name  LIMIT 1

$user->avg(['id', 'name'], 'name');
// SELECT  AVG(`id`) as id , AVG(`name`) as name FROM `easyswoole_user` GROUP BY name  LIMIT 1
```

## sum

```php
<?php
$user = new User();
$user->sum('id');
// SELECT  SUM(`id`) as id FROM `easyswoole_user` LIMIT 1

$user->sum('id', 'name');
// SELECT  SUM(`id`) as id FROM `easyswoole_user` GROUP BY name  LIMIT 1

$user->sum(['id', 'name'], 'name');
// SELECT  SUM(`id`) as id , SUM(`name`) as name FROM `easyswoole_user` GROUP BY name  LIMIT 1
```
