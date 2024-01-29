---
title: easyswoole静态资源反向代理
meta:
  - name: description
    content: 通过apache,nginx等对easyswoole框架实现静态资源反向代理处理
  - name: keywords
    content: easyswoole静态资源反向代理|swoole 静态资源处理|easyswoole 反向代理|swoole 反向代理
---
# Proxy
由于 `Swoole Server` 对 `HTTP` 协议的支持并不完整，建议仅将 `EasySwoole` 作为后端服务，并且在前端增加 `Nginx` 或 `Apache` 作为代理，参照下面的例子添加转发规则


## Nginx
```
server {
    listen 80;
    server_name local.swoole.com;
    root /data/wwwroot/;
    location / {
        proxy_http_version 1.1;
        proxy_set_header Connection "keep-alive";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        if (!-f $request_filename) {
             proxy_pass http://127.0.0.1:9501;
        }
    }
}
```

具体部署时的 `nginx` 代理配置，还可参考 [Deploy-Nginx](/Deploy/nginx.md) 章节。

::: warning 
 代理之后，可通过 `$request->getHeaderLine('x-real-ip')` 获取客户端真实ip 
:::

````php
<?php
// 控制器中获取客户端真实ip的方法
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;
class Base extends Controller
{
    /**
     * 获取用户的真实IP
     * @param string $headerName 代理服务器传递的请求头名称
     * @return string|null
     */
    protected function clientRealIP(string $headerName = 'x-real-ip'): ?string
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri = $this->request()->getHeaderLine($headerName);
        $xff = $this->request()->getHeaderLine('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有 xri 则判定为前端有 NGINX 等代理
                $clientAddress = $xri;
            } elseif (!empty($xff)) {  // 如果不存在 xri 则继续判断 xff
                $clientAddress = $xff;
            }
        }
        return $clientAddress;
    }
}
````

## Apache

```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  # RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]  fcgi 下无效
  RewriteRule ^(.*)$  http://127.0.0.1:9501/$1 [QSA,P,L]
   # 请开启 proxy_mod proxy_http_mod request_mod
</IfModule>
```

## 其他

- [项目文档仓库](https://github.com/easy-swoole/doc-3.7)

- [DEMO](https://github.com/easy-swoole/demo-3.7)

- QQ 交流群
    - VIP 群 579434607 （本群需要付费 599 元）
    - EasySwoole 官方一群 633921431(已满)
    - EasySwoole 官方二群 709134628(已满)
    - EasySwoole 官方三群 932625047(已满)
    - EasySwoole 官方四群 779897753(已满)
    - EasySwoole 官方五群 853946743(已满)
    - EasySwoole 官方六群 524475224
    
- 商业支持：
    - QQ 291323003
    - EMAIL admin@fosuss.com
        
- 作者微信

  ![](/Images/Passage/authWx.png)
    
- [捐赠](/Preface/donate.md) 您的捐赠是对 `EasySwoole` 项目开发组最大的鼓励和支持。我们会坚持开发维护下去。 您的捐赠将被用于:
        
  - 持续和深入地开发
  - 文档和社区的建设和维护
  
- `EasySwoole` 的文档使用 `EasySwoole 框架` 提供服务，采用 `MarkDown 格式` 和自定义格式编写，若您在使用过程中，发现文档有需要纠正 / 补充的地方，请 `fork` 项目的文档仓库，进行修改补充，提交 `Pull Request` 并联系我们。

