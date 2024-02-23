---
title: easyswoole注解控制器 - 行为方法注解
meta:
  - name: description
    content: easyswoole注解控制器 - 行为方法注解
  - name: keywords
    content: easyswoole注解控制器 - 行为方法注解
---

# Action 注解

控制器类 `action` 注解指的是可以在控制器类中 `action` 方法中声明使用的注解标签，包括 `Api`、`Param`、`ExtendParam`
三个注解标签。用于实现对传递到 `action` 方法的参数的约束逻辑判断及注解文档的生成。

## Api

标记当前的 `action` 为 `api`。

### 注解字段说明

#### apiName

该字段用于说明当前 `api` 在注解文档中展示的标题名称。

#### allowMethod

该字段用于限制当前 `api` 允许请求的请求方法，可配置的值可查看枚举类 `\EasySwoole\HttpAnnotation\Enum\HttpMethod`
，不配置时默认为 `[HttpMethod::GET,HttpMethod::POST]`。开发者可能会对部分接口限制只能允许 `GET` 方法请求，这时就可以配置这个字段来限制请求方法。

#### requestPath

该字段用于说明请求当前 `api`，可注册到 `fast-route`，也作为注解文档中的 `api` 请求路径。

::: tip
注意：如果不把 `Api` 注解中的 `requestPath` 注入到 `EasySwoole` 框架的 `Router`
，这个字段仅能作为注解文档声明，没有其他作用，并不会使用该字段的值作为路由提供访问，客户端实际请求时也是执行 `EasySwoole`
框架的默认解析。关于如何将 `requestPath` 注入到 `EasySwoole` 框架的 `Router` 请看下文说明。
:::

#### requestParam

该字段用于定义当前 `api action` 方法客户端需要传递的参数及限制约束规则，该字段接收一个 `Param` 对象数组。实现对传递的参数进行校验。使用示例如：

```php
<?php

namespace App\HttpController;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Optional;

class Index extends Base
{
    #[Api(
        apiName: "home",
        allowMethod: HttpMethod::GET,
        requestPath: "/test/index",
        requestParam: [
            new Param(
                name: "account",
                from: ParamFrom::GET,
                validate: [
                    new Optional()
                ],
                value: 1,
                description: new Description("翻页参数")
            )
        ],
    )]
    function index(string $account)
    {
        $this->writeJson(200, null, "account is {$account}");
    }
}
```

#### responseParam

该字段主要用于自动生成文档时，响应参数的描述说明。

#### requestExamples

该字段主要用于自动生成文档时，请求参数示例的描述说明。

#### responseExamples

该字段主要用于自动生成文档时，响应参数示例的描述说明。

#### description

该字段主要用于自动生成文档时，`api` 的描述说明。

#### 将 `Api` 注解的 `requestPath` 注入路由

修改 `App\HttpController\Router.php` 类文件，在 `initialize`
方法中添加 `\EasySwoole\HttpAnnotation\Utility::mappingRouter($routeCollector, __DIR__);` 即可。

```php
<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\HttpAnnotation\Utility;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // 将所有 `Api` 注解的 `requestPath` 注入路由
        Utility::mappingRouter($routeCollector, __DIR__);
    }
}
```

这样就可以把所有 `Api` 注解中的 `requestPath` 注入到 `fast-route`，具体用法查看 [动态路由](/HttpServer/dynamicRoute.html)
章节。

### 使用示例

```php
<?php

namespace App\HttpController;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\IsUrl;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Min;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamMiss;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use EasySwoole\HttpAnnotation\Validator\Required;

class Index extends Base
{
    #[Api(
        apiName: "home",
        allowMethod: HttpMethod::GET,
        requestPath: "/test/index.html",
        requestParam: [
            new Param(
                name: "account",
                from: ParamFrom::GET,
                validate: [
                    new Optional()
                ],
                value: 1,
                description: new Description("翻页参数")
            )
        ],
        description: new Description(__DIR__ . '/../../res/description.md', Description::MARKDOWN_FILE)
    )]
    public function index(string $account)
    {
        $this->writeJson(200, null, "account is {$account}");
    }

    #[Api(
        apiName: "hello",
        allowMethod: [HttpMethod::POST, HttpMethod::GET],
        requestPath: "/test/hello.html",
        requestParam: [
            new Param(name: "account", from: ParamFrom::GET, validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ], description: new Description("用户登录的账户Id,这个参数一定要有啊"))
        ],
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    public function hello(string $account)
    {
        $this->writeJson(200, null, "account is {$account}");
    }

    public function doc()
    {
        $path      = __DIR__;
        $namespace = 'App\HttpController';
        $doc       = new Document($path, $namespace);
        $this->response()->write($doc->scanToHtml());
    }

    #[Api(
        apiName: 'url',
        requestParam: [
            new Param(
                name: "url",
                validate: [
                    new IsUrl()
                ]
            )
        ]
    )]
    public function url()
    {

    }

    #[Api(
        apiName: 'optionalSet',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new OptionalIfParamSet("b"),
                    new MinLength("5")
                ]
            ),
            new Param(
                name: "b",
                validate: [
                    new OptionalIfParamSet("a"),
                    new Integer(),
                    new Min(1)
                ]
            )
        ]
    )]
    public function optionalSet()
    {

    }

    #[Api(
        apiName: 'optionalMiss',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new Optional(),
                    new MinLength("5")
                ],
            ),
            new Param(
                name: "b",
                validate: [
                    new OptionalIfParamMiss("a"),
                    new Integer(),
                    new Min(1)
                ]
            )
        ]
    )]
    public function optionalMiss()
    {

    }
}
```

## Param

`Param` 注解的字段说明已经在 [控制器类注解](HttpServer/Annotation/controllerClass.md) 章节进行了说明。这里就不再详细说明。
这里提到 `Param` 的使用，是其在 `action` 方法中的使用说明。

::: tip
注意：`Param` 注解在 `action` 中使用时，不能既在 `Api` 注解的 `requestParam` 字段中使用 `Param` 注解，又在 `action` 方法上单独声明 `Param` 注解，这样做时会导致后者失效。所以推荐要么在 `Api` 注解的 `requestParam` 字段中使用 `Param` 注解，要么在不使用 `Api` 注解的情况下直接单独使用 `Param` 注解，后者这种就不能把定义的 `requestPath` 注入路由，而是执行 `EasySwoole` 框架默认的路由解析模式。
:::

错误示例：

```php
<?php
namespace App\HttpController;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\Optional;

class User extends Base
{
    #[Api(
        apiName: 'optionalMiss',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new Optional(),
                    new MinLength(5)
                ],
            )
        ]
    )]
    #[Param(
        name: "b",
        validate: [
            new Optional(),
            new MinLength(5)
        ],
    )]
    public function optionalMiss()
    {

    }
}
```

上述 `optionalMiss action` 中 `Param` 注解的参数 `b` 会被忽略，既不会被验证，也不会注入参数传参。

正确示例：

```php
<?php
namespace App\HttpController;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\Optional;

class User extends Base
{
    #[Api(
        apiName: 'optionalMiss',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new Optional(),
                    new MinLength(5)
                ],
            ),
            new Param(
                name: "b",
                validate: [
                    new Optional(),
                    new MinLength(5)
                ],
            )
        ]
    )]
    public function optionalMiss()
    {

    }
}
```

### 使用示例

```php
<?php

namespace App\HttpController\Api;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Required;

#[ApiGroup(
    groupName: "Api.Auth", description: new Description(__DIR__ . '/../../../res/description.md', Description::MARKDOWN_FILE)
)]
class Auth extends ApiBase
{
    #[Api(
        apiName: "login",
        allowMethod: HttpMethod::GET,
        requestPath: "/auth/login.html",
        requestParam: [
            new Param(name: "account", from: ParamFrom::GET, validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ], description: new Description("用户登录的账户Id")),
            new Param(name: "password", from: ParamFrom::GET, validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ], description: new Description("密码")),
            new Param(name: "verify", from: ParamFrom::JSON,
                description: new Description("验证码"),
                type: ParamType::OBJECT,
                subObject: [
                    new Param(name: "code", from: ParamFrom::JSON, validate: [
                        new Required(),
                        new MaxLength(maxLen: 15),
                    ], description: "防伪编号"),
                    new Param(name: "phone", from: ParamFrom::JSON, description: "手机号")
                ])
        ],
        responseParam: [
            new Param(
                name: "code", type: ParamType::STRING
            ),
            new Param(
                name: "Result",
                type: ParamType::LIST,
                subObject: [
                    new Param("token"),
                    new Param("expire")
                ]
            ),
            new Param("msg")
        ],
        requestExamples: [
            new Example(
                [
                    new Param(name: "account", value: "1111", description: "账号"),
                    new Param(name: "password", value: "1111", description: "密码"),
                    new Param(name: "verify", value: "1111", description: new Description('验证码')),
                ]
            ),
            new Example(
                new Description(__DIR__ . '/../../../res/json.json', Description::JSON_FILE)
            ),
            new Example(
                new Description(__DIR__ . '/../../../res/xml.xml', Description::XML_FILE)
            ),
        ],
        responseExamples: [
            new Example(
                [
                    new Param(name: "result", description: "结果", subObject: [
                        new Param(name: "id", value: 1, description: "用户Id"),
                        new Param(name: "name", value: "八九", description: "昵称")
                    ]),
                    new Param(name: "code", value: "200", description: "状态码"),
                ]
            ),
            new Example(
                [
                    new Param(name: "result", value: "fail", description: "结果"),
                    new Param(name: "code", value: "500", description: "状态码"),
                ]
            ),
            new Example(
                new Description(__DIR__ . '/../../../res/json.json', Description::JSON_FILE)
            ),
            new Example(
                new Description(__DIR__ . '/../../../res/xml.xml', Description::XML_FILE)
            ),
        ],
        description: new Description("这是一个接口说明")
    )]
    public function login()
    {

    }
}
```

## ExtendParam

用于子类控制器类在重写父类控制类的 `action` 方法时限制约束传入子类控制器类的 `action` 方法参数。且 `ExtendParam` 注解只能在 `action` 中使用一次。

### 使用示例

`Base` 类，父类有一个 `add action`，限制必填参数 `param1`、`param2`。

```php
<?php
namespace App\HttpController;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;
use EasySwoole\HttpAnnotation\Validator\Required;

class Base extends AnnotationController
{
    #[Param(
        name: "param3",
        validate: [
            new Required()
        ]
    )]
    #[Param(
        name: "param4",
        validate: [
            new Required()
        ]
    )]
    public function add()
    {

    }

    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ValidateFail) {
            $this->writeJson(400, null, $throwable->getMessage());
        } else {
            if ($throwable instanceof Annotation) {
                $this->writeJson(400, null, $throwable->getMessage());
            } else {
                throw $throwable;
            }
        }
    }
}
```

`Index` 类，子类控制器，重写父类 `Base` 的 `add action`，声明 `ExtendParam` 注解指定要约束的参数，所以 `add action` 由于受到父类参数约束，所以必填参数 `param1`、`param2`。

```php
<?php
namespace App\HttpController;

use EasySwoole\HttpAnnotation\Attributes\ExtendParam;

class Index extends Base
{
    #[ExtendParam(parentParams: ['param1', 'param2'])]
    public function add()
    {

    }
}
```
