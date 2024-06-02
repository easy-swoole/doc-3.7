---
title: easyswoole 路由
meta:
  - name: description
    content: easyswoole URL解析规则 自定义路由
  - name: keywords
    content:  easyswoole URL解析规则| easyswoole 自定义路由 |swoole web框架
---

# 动态路由
动态路由就是把 `url` 的请求优雅地对应到你想要执行的操作方法。
`EasySwoole` 的动态路由是基于 [FastRoute](https://github.com/nikic/FastRoute) 实现，与其路由规则保持一致。 

## 示例代码
新建文件 `App\HttpController\Router.php`，(从框架 `3.4.x` 版本开始，用户可能不需要新建此文件。如果用户在安装时选择了释放 `Router.php` 则不必新建，如果没有，请自行新建):  

```php
<?php
namespace App\HttpController;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/user', '/user');

        $routeCollector->get('/user1', '/User/user1');
        
        $routeCollector->get('/rpc', '/Rpc/index');

        $routeCollector->get('/', function (Request $request, Response $response) {
            $response->write('this is router home');
            return false; // 不再往下请求,结束此次响应
        });
        // $routeCollector->get('/', '/index');

        $routeCollector->get('/test', function (Request $request, Response $response) {
            $response->write('this is router test.');
            return '/child';
        });
        $routeCollector->get('/child', function (Request $request, Response $response) {
            $response->write('this is router child.');
            return false; // 不再往下请求,结束此次响应
        });

        $routeCollector->get('/mtest1', '/a/b/c/d/index/index');
        $routeCollector->get('/mtest2', '/A/B/C/D/Index/index');
        
        // 从 `easyswoole/http 2.x 版本开始，绑定的参数将由框架内部进行组装到框架的 `Context(上下文)` 数据之中，具体使用请看下文。
        $routeCollector->get('/user/{id:\d+}', function (Request $request, Response $response) {
            // 获取 id 参数
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $id = $context['id'];
            $response->write("this is router user, id is {$id}");
            return false; // 不再往下请求,结束此次响应
        });
    }
}
```

## 创建路由

`EasySwoole` 路由接受一个 `URI` 和一个 `Handler`（这个 `Handler` 可以是一个 `闭包callback` 或者一个 `字符串string`），提供了一个简单优雅的方法来定义路由和行为，而不需要复杂的路由配置文件：

```php
<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/greeting', function (Request $request, Response $response) {
            $response->write('Hello World');
            return false;
        });
    }
}
```

从上面可以看到，创建匹配 `HTTP` 请求方法 `GET` 的路由的方法很简单，如下：

```php
$routeCollector->get($uri, $handler);
```

- `$uri` 为 `字符串string` 格式
- `$handler` 为 `闭包callback` 或者 `字符串string` 格式，当为 `字符串` 格式时则表示是与控制类的 `action` 相关联。

针对上述方法的参数的含义说明如下：

- 当 `$handler` 为 `/xxx` 时，则对应关联执行 `App\HttpController\Index.php` 类的 `xxx()` 方法。
- 当 `$handler` 为 `/xxx/xxx/xxx/xxx` 或者 `/Xxx/Xxx/Xxx/xxx` 时，二者其实等价，都对应关联执行 `App\HttpController\Xxx\Xxx\Xxx.php` 类的 `xxx()` 方法。
- 当 `$handler` 为 `/xxx/xxx/xxx/Xxx` 或者 `/Xxx/Xxx/Xxx/Xxx` 时，二者也等价，都对应关联执行 `App\HttpController\Xxx\Xxx\Xxx.php` 类的 `Xxx()` 方法。

综上所述，其实 `$handler` 中最后一个 `/` 后的名称一定为控制器类的 `action` 名称 (且不会转换大小写)，前面的则为对应控制器所在命名空间及路径，控制器名称及文件夹名称请务必以 `大写字母` 开头，否则路由将不能匹配到对应的执行方法。而对于 `$uri` 则没有特殊要求。`$handler` 指定路由匹配成功后需要处理的方法，可以传入一个闭包，当传入闭包时一定要 **注意处理完成之后要处理结束响应**，否则请求会继续 `Dispatch` 寻找对应的控制器来处理，当然如果利用这一点，也可以对某些请求进行处理后再交给控制器执行逻辑。

::: warning
  用户在新建控制器类和文件夹时，请使用 `大驼峰法` 命名。如果使用回调函数方式处理路由，`return false;` 代表不继续往下请求。
:::

### 默认路由文件

默认路由文件位于 `App\HttpController` 目录的 `Router.php` 文件。在 `Router.php` 文件可以定义我们常用的路由。对于大多数应用程序，也都是在 `Router.php` 文件定义路由。例如，你可以在浏览器中输入 `http://example.com/user` 来访问以下路由：

```php
<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // 下面定义的路由将 /user URI 与 App\HttpController\User 类 的 index() action 相关联
        $routeCollector->get('/user', '/User/index');
    }
}
```

### 添加额外的路由文件

上述已经提到 `EasySwoole` 框架默认的路由文件为 `App\HttpController` 目录的 `Router.php` 文件。当我们想要添加额外的路由文件时，我们可以在 `App` 目录新建一个目录 `Route` 用来统一存放额外的路由，然后在 `App\HttpController` 目录的 `Router.php` 文件中进行注册。如下：

在 `App\Route` 目录（前提：已自行创建好此目录），新增 `ApiRouter.php` 文件，该文件内容如下：

```php
<?php
namespace App\Route;

use FastRoute\RouteCollector;

class ApiRouter
{
    public function initialize(RouteCollector &$routeCollector)
    {
        $routeCollector->addGroup('/api/v1/user', function (RouteCollector $routeCollector) {
            $routeCollector->post('/create', '/Api/User/create');
            $routeCollector->post('/delete/{id:\d+}', '/Api/User/delete');
            $routeCollector->post('/update/{id:\d+}', '/Api/User/update');
            $routeCollector->post('/query', '/Api/User/query');
        });
    }
}
```

在 `App\HttpController` 目录的 `Router.php` 文件中进行注册额外的路由：

```php
<?php
namespace App\HttpController;

use App\Route\ApiRouter;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // 注册额外的路由
        (new ApiRouter())->initialize($routeCollector);
    }
}
```

### 依赖注入

在路由的回调方法中，框架会自动将当前的 `HTTP` 请求和 `HTTP` 响应注入依赖到你的路由回调中：

```php
<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/greeting', function (Request $request, Response $response) {
            $response->write('Hello World');
            return false;
        });
    }
}
```

注入的 `HTTP` 请求对象可用来读取请求参数等，注入的 `HTTP` 响应对象可用来指定返回给客户端的响应内容。

### 匹配 HTTP 方法

路由器允许你注册能响应任何 `HTTP` 请求的路由

```php
$routeCollector->get($uri, $handler);
// 等价于
$routeCollector->addRoute('GET', $uri, $handler);

$routeCollector->post($uri, $handler);
// 等价于
$routeCollector->addRoute('POST', $uri, $handler);

$routeCollector->put($uri, $handler);
// 等价于
$routeCollector->addRoute('PUT', $uri, $handler);

$routeCollector->patch($uri, $handler);
// 等价于
$routeCollector->addRoute('PATCH', $uri, $handler);

$routeCollector->delete($uri, $handler);
// 等价于
$routeCollector->addRoute('DELETE', $uri, $handler);

$routeCollector->head($uri, $handler);
// 等价于
$routeCollector->addRoute('HEAD', $uri, $handler);

$routeCollector->addRoute('OPTIONS', $uri, $handler);
```

有的时候你可能需要注册一个可响应多种 `HTTP` 请求的路由，这时你可以使用 `addRoute` 方法注册一个实现响应多种 `HTTP` 请求的路由：

```php
$routeCollector->addRoute(['GET', 'POST'], $uri, $handler);
```

### addRoute 方法说明

方法格式如下：

```php
$routeCollector->addRoute($http, $uri, $handler);
```

- `$httpMethd`（`HTTP` 请求方法）参数必须是 `大写` 的 `HTTP` 请求方法字符串或者字符串数组，如 `GET`、`POST`、`PUT`、`PATCH`、`DELETE`、`HEAD`、`OPTIONS`。
  
- `$uri` 参数需要传入一个 `URI`，格式如： `/路径名称/{参数名称:匹配规则}`，占位符 `:` 用于限制约束路由参数。
  
- `$handler` 参数需要传入一个字符串或闭包，上述已说明，就不做过多阐述。

示例如下：

```php
$routeCollector->addRoute('GET', $uri, $handler);
$routeCollector->addRoute(['GET', 'POST'], $uri, $handler);
```

## 路由参数

### 必需参数

有时你将需要捕获路由内的 `URI` 段。例如，你可能需要从 `URL` 中捕获用户的 `ID`。你可以通过定义路由参数来做到这一点：

```php
<?php
namespace App\HttpController;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/user/{id}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $id = $context['id'];
            $response->write("User {$id}");
            return false;
        });
    }
}
```

也可以根据你的需要在路由中定义多个参数：

```php
<?php
namespace App\HttpController;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/posts/{post}/comments/{comment}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $post = $context['post'];
            $comment = $context['comment'];
            $response->write("post: {$post}, comment: {$comment}");
            return false;
        });
    }
}
```

路由的参数通常都会被放在 `{}` ，并且参数名只能为字母。

### 可选参数

有时，你可能需要指定一个路由参数，但你希望这个参数是可选的。你可以在加上 `[]` 标记将 `/{参数}` 包含起来来实现：

```php
<?php
namespace App\HttpController;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/user[/{name}]', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $name = $context['name'] ?? '';
            $response->write("name: {$name}");
            return false;
        });

        // 上述路由等价于下面2个路由
        $routeCollector->get('/user', function (Request $request, Response $response) {
            // your code
            return false;
        });
        $routeCollector->get('/user/{name}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $name = $context['name'];
            $response->write("name: {$name}");
            return false;
        });
        
        
        $routeCollector->get('/user[/{id}[/{name}]]', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $name = $context['name'] ?? '';
            $response->write("name: {$name}");
            return false;
        });
    }
}
```

### 获取路由参数

#### 从 Context 中获取路由参数（路由参数的默认获取机制）

可以从 `\EasySwoole\Component\Context\ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY)` 上下文中获取路由参数。此配置项是`easyswoole/http 2.x` 版本开始的默认配置。如需设置需在 `App\HttpController\Router.php` 添加如下代码：

```php
$this->parseParams(\EasySwoole\Http\AbstractInterface\AbstractRouter::PARSE_PARAMS_IN_CONTEXT);
```

具体使用示例：

```php
<?php
namespace App\HttpController;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // /user/1
        $routeCollector->get('/user/{id}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $id = $context['id']; // 1
            $response->write("id: {$id}");
            return false;
        });
    }
}
```

#### 从 Query Param 中获取路由参数

如果想从 Query Param 中获取路由参数，可使用这个 `$this->request()->getQueryParams()` 方法进行获取，但是需要先在 `App\HttpController\Router.php` 中进行设置：

```php
$this->parseParams(\EasySwoole\Http\AbstractInterface\AbstractRouter::PARSE_PARAMS_IN_GET);
```

具体设置如下：

```php
<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->parseParams(Router::PARSE_PARAMS_IN_GET);

        // /user/1
        $routeCollector->get('/user/{id:\d+}', function (Request $request, Response $response) {
            $id = $request->getQueryParam('id'); // 1
            $response->write("id: {$id}");
            return false;
        });
    }
}
```

#### 从 POST 请求参数中获取路由参数

如果想从 POST 请求参数中获取路由参数，可使用这个 `$this->request()->getParsedBody()` 方法进行获取，但是需要先在 `App\HttpController\Router.php` 中进行设置：

```php
$this->parseParams(\EasySwoole\Http\AbstractInterface\AbstractRouter::PARSE_PARAMS_IN_POST);
```

具体设置如下：

```php
<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->parseParams(Router::PARSE_PARAMS_IN_POST);

        // /user/1
        $routeCollector->get('/user/{id:\d+}', function (Request $request, Response $response) {
            $id = $request->getParsedBody('id'); // 1
            $response->write("id: {$id}");
            return false;
        });
    }
}
```


#### NONE

不获取路由参数时，可以在 `App\HttpController\Router.php` 中进行设置:

```php
$this->parseParams(\EasySwoole\Http\AbstractInterface\AbstractRouter::PARSE_PARAMS_NONE);
```

> 注意：以上 4 种设置，用户只能设置 1 种。`Router` 默认使用的设置是从请求上下文 Context 中获取路由参数。

::: tip
`easyswoole/http 2.x` 之前版本绑定的参数将由框架内部进行组装到框架的 `Query Param` 数据之中，调用方式如下：
:::

```php
<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // /user/1
        $routeCollector->get('/user/{id:\d+}', function (Request $request, Response $response) {
            $id = $request->getQueryParam('id'); // 1
            $response->write("id: {$id}");
            return false;
        });
    }
}
```

### 参数约束验证

你可以在路由参数后面添加正则表达式来限制路由参数的格式：

```php
<?php
namespace App\HttpController;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/user/{name:.+}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $name = $context['name'];
            $response->write("name: {$name}");
            return false;
        });
        $routeCollector->get('/user1/{name:[A-Za-z]+}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $name = $context['name'];
            $response->write("name: {$name}");
            return false;
        });
        
        // 将限制 `/users/` 后面的id参数，只能是数字 `[0-9]`
        $routeCollector->get('/user/{id:\d+}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $id = $context['id'];
            $response->write("id: {$id}");
            return false;
        });
        $routeCollector->get('/user1/{id:[0-9]+}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $id = $context['id'];
            $response->write("id: {$id}");
            return false;
        });

        $routeCollector->get('/user2/{id:[0-9]+}/{name:[a-z]+}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $id = $context['id'];
            $name = $context['name'];
            $response->write("id: {$id}, name: {$name}");
            return false;
        });
    }
}
```

### 路由参数中的斜杠字符

路由允许除 `/` 之外的所有字符出现在路由参数值中。 你必须使用正则表达式明确允许 `/` 成为占位符的一部分：

```php
<?php
namespace App\HttpController;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/search/{search:.*}', function (Request $request, Response $response) {
            $context = ContextManager::getInstance()->get(Router::PARSE_PARAMS_CONTEXT_KEY);
            $search = $context['search'];
            $response->write("search: {$search}");
            return false;
        });
    }
}
```

## 路由分组

路由分组允许你共享 `URI` 前缀，而无需在每个单独的路由上定义这些 URI` 前缀。

嵌套组尝试智能地将 `URI` 前缀与其父组 “合并”。`URI` 前缀中的斜杠会在适当的地方自动添加。

```php
<?php
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

$routeCollector->addGroup('/admin', function (RouteCollector $collector) {
    $collector->addRoute('GET', '/do-something', function (Request $request, Response $response) {
        $response->write('this is do-something');
        return false;
    });
    $collector->addRoute('GET', '/do-another-thing', function (Request $request, Response $response) {
        $response->write('this is do-another-thing');
        return false;
    });
    $collector->addRoute('GET', '/do-something-else', function (Request $request, Response $response) {
        $response->write('do-something-else');
        return false;
    });
});

// 和上述路由等价
$routeCollector->addRoute('GET', '/admin/do-something', function (Request $request, Response $response) {
    $response->write('this is do-something');
    return false;
});
$routeCollector->addRoute('GET', '/admin/do-another-thing', function (Request $request, Response $response) {
    $response->write('this is do-another-thing');
    return false;
});
$routeCollector->addRoute('GET', '/admin/do-something-else', function (Request $request, Response $response) {
     $response->write('do-something-else');
     return false;
});
```

## 特殊的路由

### 从路由调度到其他路由

如果要定一个调度到另一个 `URI` 的路由，可以使用 `return` 的方式，可快速实现类似重定向的功能，而不需要去定义完整的路由或者控制器：

```php
$routeCollector->addRoute('GET', '/here', function (Request $request, Response $response) {
     return '/there';
});
```

```php
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

$routeCollector->addGroup('/admin', function (RouteCollector $collector) {
    // /admin/test?version=1
    // /admin/test?version=2
    // /admin/test?version=3
    $collector->addRoute('GET', '/test', function (Request $request, Response $response) {
        $version = $request->getQueryParam('version');

        if ($version == 1) {
            $path = '/V1' . $request->getUri()->getPath(); // "/V1/admin/test"
        } else {
            // /V2/admin/test
            $path = '/V2' . $request->getUri()->getPath(); // "/V2/admin/test"
        }

        // return "/V1/admin/test";
        // return "/V2/admin/test";
        return $path;
    });
});

// 注意：/admins/index?version=x 不能匹配到下面这个 action 路由配置参数
// 需要单独配置路由，如下所示：即执行对应的 App\HttpController\V1\Admins.php 类的 index() 方法
// $routeCollector->addRoute('GET', '/admins/index', '/V1/Admin/index');
$routeCollector->addGroup('/admins', function (RouteCollector $collector) {
    // /admin/test?version=1
    // /admin/test?version=2
    // /admin/test?version=3
    $collector->addRoute('GET', '/{action}', function (Request $request, Response $response) {
        $version = $request->getQueryParam('version');

        if ($version == 1) {
            $path = '/V1' . $request->getUri()->getPath(); // "/V1/admins/test"
        } else {
            $path = '/V2' . $request->getUri()->getPath(); // "/V2/admins/test"
        }

        // return "/V1/admin/test";
        // return "/V2/admin/test";
        return $path;
    });
});
```

## 全局模式拦截

在 `Router.php` 加入以下代码，即可开启全局模式拦截

```php
$this->setGlobalMode(true);
```

全局模式拦截下，路由将只匹配 `Router.php` 中指定的 `$handler` 的控制器方法进行响应，将不会执行框架的默认解析。

## 异常错误处理  

通过以下 `2` 个方法，可设置 `路由HTTP请求方法无法匹配` 以及 `路由无法匹配` 的处理机制：

在 `Router.php` 加入以下代码：

```php
<?php
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

// 路由HTTP请求方法无法匹配
$this->setMethodNotAllowCallBack(function (Request $request, Response $response) {
    $response->withStatus(404);
    return false; // 结束此次响应
});

// 路由未知，无法匹配
$this->setRouterNotFoundCallBack(function (Request $request, Response $response){
    $response->withStatus(404);
    return 'index'; // 重定向到 index 路由
});
```

::: warning 
  该回调函数只针对于 `fastRoute` 未匹配状况，如果回调里面不结束该请求响应，则该次请求将会继续进行 `Dispatch` 并尝试寻找对应的控制器进行响应处理。  
:::
