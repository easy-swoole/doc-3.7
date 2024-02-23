---
title: easyswoole注解控制器 - 安装
meta:
  - name: description
    content: easyswoole注解控制器 - 安装
  - name: keywords
    content:  easyswoole注解控制器 - 安装
---

# 安装

```bash
composer require easyswoole/http-annotation=3.x
```

## 组件要求

- php: >=8.1
- ext-json: *
- psr/http-message: ^1.0
- easyswoole/http: 3.x
- ext-mbstring: *
- ext-dom: *
- ext-simplexml: *
- ext-libxml: *
- easyswoole/parsedown: ^1.0

::: tip
  注意：用户在使用 `EasySwoole 注解控制器` 进行 `EasySwoole` 项目开发时，仍需要 `use` 注解相对应的命名空间。这显然不是一个高效的做法。我们推荐在 `PhpStorm` 环境下进行开发，并且在 `PhpStorm` 中安装 `Jetbrain` 自带的 `PHP Annotation` 组件，可以提供注解命名空间自动补全、注解属性代码提醒、注解类跳转等非常有帮助的。(`PhpStorm 2019` 以上版本的 `IDE`，该组件可能不能正常使用。)
:::

## 组件优势

::: tip
  在使用 `EasySwoole Http` 注解控制器组件进行开发时，可以很方便地生成 `API` 接口文档，可以极大地提高了我们 `phper` 的开发效率。具体如何使用请看 [自动注解文档](/HttpServer/Annotation/doc.md) 章节。
:::

## IDE 提示

::: tip
当代码中使用注解与 `EasySwoole` 提供的 `Param` 冲突，无法实现 `IDE` 提示，可以使用别名的方式去使用 `EasySwoole` 的 `Param` 注解，代码如下。
:::

```php
<?php
namespace App\HttpController;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param as ReqParam;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Required;

class Index extends AnnotationController
{
    #[ReqParam(
        name: "deviceId",
        from: ParamFrom::JSON,
        validate: [
            new Required("deviceId is required.")
        ]
    )]
    function reportAction()
    {

    }
}
```

## 实现原理

注解控制器，完整命名空间为 ```EasySwoole\HttpAnnotation\AnnotationController```，是继承自 ```use EasySwoole\Http\AbstractInterface\Controller```的子类。它重写了父类的```__hook```方法，从而实现对注解控制器的支持。

#### __hook

在 `__hook` 方法中，自动解析使用在当前控制器类上的注解。 该方法是承接 ```Dispatcher``` 与控制器实体逻辑的桥梁。在该方法中，注解控制器做了以下事情：

- 检查当前请求的 `http` 请求方法是否在当前控制器类的 `action` 允许的请求方法范围内，来实现过滤非法请求的目的（前提：使用了 `Api` 注解的 `allowMethod` 属性限制）。
- 读取在控制器类的 `action` 中使用的 `Api` 注解信息，读取在控制器类的 (非静态非只读的 `public` 或 `protected` 修饰的) 成员变量中使用的 `Context/Di/Inject` 注解信息并自动给成员变量赋值。
- 检查并执行成员变量注解逻辑
- 检查 ```onRequest``` 函数注解参数并执行注解参数逻辑校验
- 检查使用在 `action` 上的注解标签并进行参数校验与逻辑校验

# 基础示例

```php
<?php
namespace App\HttpController;

use EasySwoole\EasySwoole\Trigger;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;
use EasySwoole\HttpAnnotation\Validator\Required;

class Index extends AnnotationController
{
    #[Param(
        name: "name",
        validate: [
            new Required("")
        ]
    )]
    #[Param(
        name: "age",
        validate: [
            new Required("")
        ]
    )]
    public function index()
    {
        $data = $this->request()->getRequestParam();
        $this->response()->write("your name is {$data['name']} and age {$data['age']}");
    }

    public function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ValidateFail) {
            $this->response()->withHeader('Content-type', 'text/html;charset=utf-8');
            $this->response()->write("字段【{$throwable->getFailRule()->currentCheckParam()->name}】校验错误");
        } else {
            Trigger::getInstance()->throwable($throwable);
        }
    }
}
```

在以上代码中，会自动对 ```name``` 和 ```age``` 字段进行校验，当校验失败时，抛出一个异常，校验成功则进入 `index action` 逻辑。具体请看成员属性注解章节。
