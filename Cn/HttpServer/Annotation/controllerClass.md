---
title: easyswoole注解控制器-控制器类注解
meta:
  - name: description
    content: easyswoole注解控制器-控制器类注解
  - name: keywords
    content:  easyswoole注解控制器-控制器类注解
---

# 控制器类注解

控制器类注解指的是可以在控制器类上声明使用的注解标签，包括 `Param`、`ApiGroup` 两个注解标签。用于实现对控制器类中成员方法的参数的约束逻辑判断及注解文档的生成。

## Param 注解

`Param` 注解，作用域在控制器类声明中生效，可作为当前控制器类的全局参数去使用。例如在以下代码中：

```php
<?php
namespace App\HttpController;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Required;

#[Param(
    name: "signature",
    validate: [
        new Required()
    ],
    ignoreAction: [
        "info"
    ]
)]
class Profile extends AnnotationController
{
    public function info() {
        
    }
    
    public function foo($signature) {
        $data = $this->request()->getRequestParam();
        $this->response()->write("your name is {$name} and age {$age}");
    }
}
```

那么则规定了 `Profile` 这个控制器类除了 `info` 这个 `action` 不需要 `signature` 参数，其他 `action` 均需要 `signature` 参数，且校验规则分别为 `required` 即要求必填。

### 参数的接收

#### 自动传参

```php
<?php
namespace App\HttpController;

use EasySwoole\EasySwoole\Trigger;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;
use EasySwoole\HttpAnnotation\Validator\Required;

#[Param(
    name: "signature",
    validate: [
        new Required()
    ],
    ignoreAction: [
        "info"
    ]
)]
class Profile extends AnnotationController
{
    public function foo($signature)
    {
        $this->response()->write("the signature is {$signature}");
    }
}
```

当某个 `action` 定义了参数，且在控制器类声明中使用 `Param` 注解的时候，那么控制器会利用反射机制，根据 `action` 方法定义的参数名，去自动获取取对应的参数。

### Param 注解附加的字段

`Param` 注解除了 `name` 字段为必填项，还有以下几个辅助字段。

#### from

例如在以下注解中：

```php
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Required;

#[Param(
    name: "name",
    from: [ParamFrom::GET, ParamFrom::POST],
    validate: [
        new Required(),
        new MaxLength(25),
    ]
)]
#[Param(
    name: "age",
    from: [ParamFrom::POST],
    validate: [
        new Integer(),
    ]
)]
class Profile extends AnnotationController
{

}
```

则规定了 `name` 字段允许的取参顺序为：GET => POST，而 `age` 参数就仅仅允许为 `POST` 传参。目前 `from` 的允许值可查看枚举类 `\EasySwoole\HttpAnnotation\Enum\ParamFrom`。在不规定 `from` 字段时，默认的 `from` 值为 `[ParamFrom::GET, ParamFrom::POST]`。具体实现可在 `\EasySwoole\HttpAnnotation\Attributes\Param` 的 `parsedValue` 方法中查看。

#### validate

对请求中传入的参数设置验证规则，并进行验证，验证失败则抛出异常 `\EasySwoole\HttpAnnotation\Exception\ValidateFail`。

#### value

在客户端没有传递该参数的值时，可以用该字段进行默认值的定义。

#### description

该字段主要用于自动生成文档时，参数的描述说明。

#### type

例如以下注解中：

```php
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamType;

#[Param(
    name: "age",
    type: ParamType::INT,
)]
class Profile extends AnnotationController
{
    public function echoAge($age)
    {
        var_dump('the is age');
        var_dump($age);
        $this->response()->write("the age is {$age}");
    }
}
```

通过 `action` 方法自动传参得到的参数时，会对 `age` 这个参数进行 `intval()` 处理。`type` 字段可选值可查看枚举类 `\EasySwoole\HttpAnnotation\Enum\ParamType`，具体处理原理可在 `\EasySwoole\HttpAnnotation\Attributes\Param` 类的 `parsedValue` 方法中查看。

#### subObject

该字段用于对当前参数为字典类型时，对其子属性进行限制约束。如：

```php
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Required;

#[Param(
    name: 'result',
    from: ParamFrom::JSON,
    validate: [
        new Required(),
    ],
    description: new Description('result'),
    subObject: [
        new Param(
            name: "userName",
            from: ParamFrom::JSON,
            validate: [
                new Required()
            ]
       )
    ]
)]
class Api extends AnnotationController
{

}
```

上述示例要求客户端传参时，必传参数 `result` 对象中必须包含子属性 `userName`。

#### ignoreAction

该字段用于声明需要对当前控制类的哪些 `action` 不进行注入，或者不做参数限制约束。

## ApiGroup 注解

该注解用于声明在控制器类的声明中，用于注解文档的生成。

```php
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\AnnotationController;

#[ApiGroup(
    groupName: "Api",
    description: new Description(
        desc: EASYSWOOLE_ROOT . "/res/description.md"
    ),
)]
class ApiBase extends Base
{

}
```

### groupName

该字段用于给接口分组，它会自动把相同分组的接口统一在同一个分类下，方便开发者查看接口文档。

### description

该字段用于说明接口文档存放的位置及接口文档生成格式。
