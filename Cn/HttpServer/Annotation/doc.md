---
title: easyswoole注解控制器 - 注解自动生成文档
meta:
  - name: description
    content: easyswoole注解控制器 - 注解自动生成文档
  - name: keywords
    content: easyswoole注解控制器 - 注解自动生成文档
---

# 注解文档

`EasySwoole` 允许对使用了注解控制器的注解的控制器类及 `action`，生成 `api` 接口文档。

## 控制器输出文档

```php
<?php
namespace App\HttpController;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Document\Document;

class Index extends AnnotationController
{
    public function doc()
    {
        $path      = __DIR__;
        $namespace = 'App\HttpController';
        $doc       = new Document($path, $namespace);
        $this->response()->withAddedHeader('Content-Type', "text/html;charset=utf-8");
        $this->response()->write($doc->scanToHtml());
    }
}
```

例如在以上的代码中，我们就是直接扫描 `EasySwoole` 框架默认的控制器目录下的使用控制器注解的所有控制器类并输出对应文档，用户可以自己去做文档权限控制，或者是对应的目录限制。

## 生成离线文档

### 注册生成离线文档命令

```bash
php easyswoole.php doc
```

在使用命令之前需要先在 `EasySwoole` 框架中注册生成离线文档命令，修改 `EasySwoole` 框架根目录的 `bootstrap.php` 文件，如下：

```php
<?php
// bootstrap.php
// 全局bootstrap事件
date_default_timezone_set('Asia/Shanghai');

\EasySwoole\Command\CommandManager::getInstance()->addCommand(new \App\Command\DocCommand());
```

`DocCommand` 类实现如下：

```php
<?php

namespace App\Command;

use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\Command\CommandManager;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\HttpAnnotation\Document\Document;

class DocCommand implements CommandInterface
{
    public function commandName(): string
    {
        return 'doc';
    }

    public function exec(): ?string
    {
        $dir = CommandManager::getInstance()->getOpt("dir", EASYSWOOLE_ROOT . '/App/HttpController');
        if (empty($dir)) {
            return "php easyswoole.php doc --dir=DIR";
        }

        $fix      = "doc_" . date("Ymd");
        $maxCount = 1;
        if ($dh = opendir(getcwd())) {
            while (($file = readdir($dh)) !== false) {
                if (is_file($file)) {
                    if (str_starts_with($file, $fix)) {
                        $name  = explode(".", $file)[0];
                        $count = (int)substr($name, strlen($fix) + 1);
                        if ($count >= $maxCount) {
                            $maxCount = $count + 1;
                        }
                    }
                }
            }
            closedir($dh);
        }

        $finalFile = getcwd();

        $namespace = 'App\HttpController';
        $doc       = new Document($dir, $namespace);
        $html      = $doc->scanToHtml();
        $finalFile = $finalFile . "/{$fix}_{$maxCount}.html";
        file_put_contents($finalFile, $html);

        return "create doc file :{$finalFile}";
    }

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        $commandHelp->addActionOpt('--dir', 'scanned directory or file');
        return $commandHelp;
    }

    public function desc(): string
    {
        return 'build api doc by annotations';
    }
}
```

在项目根目录下执行如下命令：

```bash
php easyswoole.php doc
```

即可生成对应的离线文档。

> 注意，仅当有使用了 `Api` 注解的控制器方法才会被渲染到离线文档中。

## 注解使用示例

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
