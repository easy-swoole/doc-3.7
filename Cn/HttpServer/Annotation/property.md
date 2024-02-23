---
title: easyswoole注解控制器 - 成员属性注解
meta:
  - name: description
    content: easyswoole注解控制器 - 成员属性注解
  - name: keywords
    content:  easyswoole注解控制器 - 成员属性注解
---

# 成员属性注解

我们直接看以下示例：

`UserService` 类

```php
<?php
namespace App\Service;

class UserService
{
    public function info()
    {
        var_dump("this is user info");
    }
}
```

`Index` 控制器类

```php
<?php
namespace App\HttpController;

use App\Service\UserService;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Attributes\Property\Inject;

class Index12 extends AnnotationController
{
    #[Inject(object: new UserService())]
    protected ?UserService $param1;

    #[Di(key: 'param2Key')]
    protected ?UserService $param2;

    #[Context(key: 'param3Key')]
    protected ?UserService $param3;

    protected function onRequest(?string $action): ?bool
    {
        return parent::onRequest($action);
    }

    public function test()
    {
        $this->param1->info();
        $this->param2->info();
        $this->param3->info();
    }
}
```

如果想正常注入 `param2` 和 `param3` 参数，我们可以在框架的全局 onRequest 事件中进行注入，如下：

```php
<?php

namespace EasySwoole\EasySwoole;

use App\Service\UserService;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST, function (Request $request, Response $response): bool {
            // 提前使用 Di 注册 param2 参数
            Di::getInstance()->set('param2Key', UserService::class); // param2 参数也可在 bootstrap、initialize、mainServerCreate 等事件中提前注册。

            // 提前使用 ContextManager 注册 param3 参数
            ContextManager::getInstance()->set('param3Key', new UserService()); // param3 参数只可在全局 onRequest 事件中提前注册。

            return true;
        });
    }

    public static function mainServerCreate(EventRegister $register)
    {

    }
}
```

## Context 注解

```Context``` 注解，完整命名空间是 ```\EasySwoole\HttpAnnotation\Attributes\Property\Context```，用于在每次请求进来的时候，从上下文管理器中取数据，并赋值到对应的属性中，以上等价于:
```
$this->param3 = \EasySwoole\Component\ContextManager::getInstance()->get('param3Key');
```

## Di 注解

```Di``` 注解，完整命名空间是 ```\EasySwoole\HttpAnnotation\Attributes\Property\Di```，用于在每次请求进来的时候，从 `IOC` 中取数据，并赋值到对应的属性中，以上等价于:
```
$this->param2 = \EasySwoole\Component\Di::getInstance()->get('param2Key');
```

## Inject 注解

```Inject```注解，完整命令空间是 ```\EasySwoole\HttpAnnotation\Attributes\Property\Inject```，可注入类并且传入构造函数参数，以上等价于: 
```
$this->param1 = new \App\Service\UserService(...$args)
```
