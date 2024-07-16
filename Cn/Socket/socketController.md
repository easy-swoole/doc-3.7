---
title: easyswoole socket
meta:
- name: description
  content: easyswoole/socket
- name: keywords
  content: easyswoole socket|swoole tcp udp websocket
---

# Socket 控制器

## 组件安装

> composer require easyswoole/socket

## Examples

关于 `Socket` 控制器使用的具体示例，请查看 [demo](https://github.com//Stitch-June/EasySwooleSocketDemo)

## 包解析与控制器逻辑

### 数据解析与控制器映射

数据解析和控制器映射，开发者可以通过实现 `\EasySwoole\Socket\AbstractInterface\ParserInterface` 接口的来实现，然后在 `encode` 方法中实现数据解析和控制器映射。使用方法可以参考下面的示例。

下面以实现一个 `tcp socket` 控制器为例。首先定义协议解析器类 `TcpParser` 类，该类需要实现 `\EasySwoole\Socket\AbstractInterface\ParserInterface` 接口。如下：

```php
<?php
namespace App\Parser;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;

class TcpParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        // 数据解析，这里采用简单的json格式作为应用层协议
        $data       = substr($raw, 4);
        $data       = json_decode($data, true);
        
        // 实现与控制器和action的映射
        $caller     = new Caller();
        $controller = !empty($data['controller']) ? $data['controller'] : 'Index';
        $action     = !empty($data['action']) ? $data['action'] : 'index';
        $param      = !empty($data['param']) ? $data['param'] : [];
        $controller = "App\\TcpController\\{$controller}";
        $caller->setControllerClass($controller);
        $caller->setAction($action);
        $caller->setArgs($param);
        return $caller;
    }
    
    // ... encode 方法
}
```

### 数据的打包与响应

对于数据的打包，开发者可以通过实现 `\EasySwoole\Socket\AbstractInterface\ParserInterface` 接口的来实现，然后在 `decode` 方法中实现数据的打包。使用方法可以参考下面的示例。

```php
<?php
namespace App\Parser;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Response;

class TcpParser implements ParserInterface
{
    // ... decode 方法

    public function encode(Response $response, $client): ?string
    {
        // 实现对数据的打包
        return pack('N', strlen(strval($response->getMessage()))) . $response->getMessage();
    }
}
```

关于对数据的响应，则需要开发者在控制器的 `action` 进行处理，调用 ```$this->response()->setMessage($message)``` 进行响应调用端。参考示例如下：

```php
<?php
namespace App\TcpController;

use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        // 这里我们响应一个字符串'this is index'给调用端
        $this->response()->setMessage('this is index');
    }
}
```
