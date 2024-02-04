---
title: easyswoole ORM分页查询
meta:
  - name: description
    content: easyswoole ORM分页查询
  - name: keywords
    content:  easyswoole ORM分页查询
---

# 分页查询 page

- 方法说明：

```\EasySwoole\FastDb\Beans\Query::page``` 方法

```php
function page(?int $page,bool $withTotalCount = false,int $pageSize = 10): Query
```

- 使用示例：

```php
// 使用条件的分页查询 不进行汇总 withTotalCount=false
// 查询 第1页 每页10条 page=1 pageSize=10
$user = new User();
$user->queryLimit()->page(1, false, 10);
$resultObject = $user->all();
foreach ($resultObject as $oneUser) {
    var_dump($oneUser->name);
}

// 使用条件的分页查询 进行汇总 withTotalCount=true
// 查询 第1页 每页10条 page=1 pageSize=10
$user = new User();
$user->queryLimit()->page(1, true, 10)->where('id', 3, '>');
$resultObject = $user->all();
$total = $resultObject->totalCount(); // 汇总数量
foreach ($resultObject as $oneUser) {
    var_dump($oneUser->name);
}
var_dump($total);
```
