---
title: easyswoole ORM-条件构造类QueryClass
meta:
  - name: description
    content: easyswoole ORM-Query查询条件构造类QueryClass
  - name: keywords
    content: easyswoole ORM-Query查询条件构造类QueryClass
---

# 条件构造类 Query

`\EasySwoole\FastDb\Beans\Query` 类用于构建在模型中使用构造查询、更新、删除等条件。

支持的方法有：
- `limit(int $num,bool $withTotalCount = false):Query`
- `page(?int $page,bool $withTotalCount = false,int $pageSize = 10):Query`
- `fields(?array $fields = null,bool $returnAsArray = false):Query`
- `hideFields(array|string $hideFields):Query`
- `getHideFields():?array`
- `getFields():?array`
- `orderBy($orderByField, $orderbyDirection = "DESC", $customFieldsOrRegExp = null):Query`
- `where(string $col, mixed $whereValue, $operator = '=', $cond = 'AND'):Query`
- `orWhere(string $col, mixed $whereValue, $operator = '='):Query`
- `join($joinTable, $joinCondition, $joinType = ''):Query`
- `func(callable $func):Query`
- `returnEntity():AbstractEntity`

调用模型中的 `queryLimit()` 的返回值即为 `\EasySwoole\FastDb\Beans\Query` 类，方便开发者处理复杂的查询条件。

## 查询时示例

```php
$user = new User();
$user->queryLimit()->where('name', 'easyswoole');   # 使用 Query 类的 where 方法
$userModel = $user->find();
echo $userModel->name;
```

## 更新时示例

```php
$user = new User();
$user->queryLimit()->where('id', 1);   # 使用 Query 类的 where 方法
$user->updateWithLimit(['name' => 'easyswoole']);
```
