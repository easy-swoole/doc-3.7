---
title: easyswoole ORM数据分批处理
meta:
  - name: description
    content: easyswoole ORM数据分批处理
  - name: keywords
    content:  easyswoole ORM数据分批处理
---

# 数据分批处理 chunk

模型也支持对返回的数据分批处理。特别是如果你需要处理成千上百条数据库记录，可以考虑使用 `chunk` 方法，该方法一次获取结果集的一小块，然后填充每一小块数据到要处理的闭包，该方法在编写处理大量数据库记录的时候非常有用。

比如，我们可以全部用户表数据进行分批处理，每次处理 `20` 个用户记录：

```php
<?php
(new User())->chunk(function (User $user) {
    // 处理 user 模型对象
    $user->updateWithLimit(['status' => 1]);
}, 20);
```
