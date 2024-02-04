---
title: easyswoole ORM自定义返回结果类型
meta:
  - name: description
    content: easyswoole ORM自定义返回结果类型
  - name: keywords
    content:  easyswoole ORM自定义返回结果类型
---

# 自定义返回结果类型

`findAll()` 方法的 `returnAsArray` 参数可以设置查询的返回对象的名称（默认是模型对象）。

```php
<?php
$returnAsArray = true;
(new User())->findAll(null, null, $returnAsArray);
```

`all()` 方法调用 `queryLimit()` 方法的 `fields()` 方法的 `returnAsArray` 参数可以设置查询的返回对象的名称（默认是模型对象）。

```php
<?php
$returnAsArray = true;
(new User())->queryLimit()->fields(null, $returnAsArray);
```
