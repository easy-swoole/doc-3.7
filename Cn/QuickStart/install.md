---
title: easyswoole安装教程
meta:
  - name: description
    content: easyswoole安装教程
  - name: keywords
    content: easyswoole安装教程|swoole框架安装教程
---


# 框架安装

- [GitHub](https://github.com/easy-swoole/easyswoole)  喜欢记得给我们点个 ***star***
- [Github for Doc](https://github.com/easy-swoole/doc-3.7) 文档贡献

::: danger 
注意事项，请看完再进行安装
:::

- 框架使用 `Composer` 作为依赖管理工具，在开始安装框架前，请确保已经按上一章节的要求配置好环境并安装好了 `Composer` 工具
- 关于 `Composer` 的安装可以参照 [Composer官网安装教程](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) 或 [Composer中国全量镜像](https://pkg.phpcomposer.com/#how-to-install-composer) 的安装教程
- 目前推荐的镜像为 [Composer官网镜像](https://packagist.org/)【可能需要梯子】或 [腾讯云镜像](https://mirrors.cloud.tencent.com/composer/) 或 [阿里云镜像](https://mirrors.aliyun.com/composer/) 
- 在安装过程中，会提示用户选择是否释放框架的 `Index.php`、`Router.php` 文件到项目目录(默认不会释放，也不会生成 `App` 目录)，请保证项目目录有可写入权限
- 安装完成之后，如果没有自动生成 `App` 目录，请自行根据 [Hello World 章节](/QuickStart/helloworld.md) 章节进行配置
- 部分机器(例如 `Docker` 环境)在使用框架时遇到类似 `DNS Lookup resolve failed...` 错误，请更换机器的 `DNS` 为阿里云公共 DNS `223.5.5.5` 和 `223.6.6.6`。具体更换步骤可查看 [更换 DNS](https://www.alidns.com/knowledge?type=SETTING_DOCS#user_linux)


## 框架更新说明(安装之前必看)

- [更新说明](/Update/main.md)

很多小伙伴，在使用 `Swoole 4.6.x` 扩展时，在使用 `EasySwoole` 进行开发时（一般是在使用到 `Swoole` 协程时），遇到了类似如下错误：`\[FATAL ERROR\]: all coroutines
 (count: 1) are asleep - deadlock!`，这个错误属于 `Swoole 4.6.x` 扩展的 `debug` 信息，详细可查看 [协程配置](https://wiki.swoole.com/#/coroutine/coroutine?id=deadlock_check)，如果不想看到这个信息，可在 `bootstrap` 事件（即项目根目录的 `bootstrap.php` 文件）中最开始的位置，对协程的使用进行配置。配置示例代码如下：

```php
<?php
date_default_timezone_set('Asia/Shanghai');
// 关闭死锁检测相关堆栈信息输出到日志
\Swoole\Coroutine::set(['enable_deadlock_check' => false]);
```

## 通过 Composer 安装

### 切换 Composer 镜像
```
# 推荐使用composer官方镜像（如果使用此镜像还是不行请自行更换其他源下载）
composer config -g repo.packagist composer https://repo.packagist.org

# 使用腾讯云 Composer 镜像
composer config -g repo.packagist composer https://mirrors.tencent.com/composer/

# 使用阿里云 Composer 镜像
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

删除镜像
```
composer config -g --unset repos.packagist
```

### Composer 安装 EasySwoole 框架

按下面的步骤进行手动安装

```bash
composer require easyswoole/easyswoole=3.7.x
php vendor/easyswoole/easyswoole/bin/easyswoole.php install
```

或者
```bash
composer require easyswoole/easyswoole=3.7.x
php vendor/bin/easyswoole.php install
```

## Docker 下开发

如果您的本机环境并不能达到 `EasySwoole` 的运行环境要求，或对于环境配置不是那么熟悉，那么您可以通过以下方法来运行及开发 `EasySwoole` 项目：

### 启动容器

可以根据实际情况，映射到宿主机对应的目录，以下以 `/wrokspace/easyswoole` 为例。这个映射目录您可以根据实际进行修改。

::: warning
如果 docker 启动时开启了 selinux-enabled 选项，容器内访问宿主机资源就会受限，所以启动容器时可以增加 --privileged -u root 选项
:::

```bash
docker run --name easyswoole \
-v /workspace/easyswoole:/var/www \
-p 9501:9501 -it \
--privileged -u root \
--entrypoint /bin/sh \
easyswoolexuesi2021/easyswoole:php8.1.22-alpine3.16-swoole5.1.1
```

EasySwoole 支持的 Docker 镜像目前有如下：

- easyswoolexuesi2021/easyswoole:php8.1.22-alpine3.16-swoole5.1.1：环境为 `php8.1.22` + `swoole5.1.1` + `easyswoole 3.7.x`，构建镜像 `Dockerfile` 可[查看](https://github.com/XueSiLf/easyswoole-docker/blob/main/dockerfiles/php8/8.1.22/alpine/3.16/swoole/5.1.1/Dockerfile)
- easyswoolexuesi2021/easyswoole:php7.4.33-alpine3.15-swoole4.4.26：环境为 `php7.4.33` + `swoole4.4.26` + `easyswoole 3.5.x`，构建镜像 `Dockerfile` 可[查看](https://github.com/XueSiLf/easyswoole-docker/blob/main/dockerfiles/php7/7.4.33/alpine/3.15/swoole/4.4.26/Dockerfile)
- easyswoolexuesi2021/easyswoole:php7.3.33-alpine3.12-swoole4.4.26：环境为 `php7.3.33` + `swoole4.4.26` + `easyswoole 3.5.x`，构建镜像 `Dockerfile` 可[查看](https://github.com/XueSiLf/easyswoole-docker/blob/main/dockerfiles/php7/7.3.33/alpine/3.12/swoole/4.4.26/Dockerfile)

### 创建及安装框架

```bash
cd /var/www
composer require easyswoole/easyswoole=3.7.x
php vendor/easyswoole/easyswoole/bin/easyswoole.php install
```

或者
```bash
cd /var/www
composer require easyswoole/easyswoole=3.7.x
php vendor/bin/easyswoole.php install
```

## 安装成功

执行安装之后，安装成功则会有如下界面:

::: warning
首次安装，会提示您是否选择释放(release) `Index.php`、`Router.php` 和 `App` 目录到项目目录，执行界面如下：
:::

```bash
php vendor/easyswoole/easyswoole/bin/easyswoole.php install
 ______                          _____                              _        
|  ____|                        / ____|                            | |       
| |__      __ _   ___   _   _  | (___   __      __   ___     ___   | |   ___ 
|  __|    / _` | / __| | | | |  \___ \  \ \ /\ / /  / _ \   / _ \  | |  / _ \
| |____  | (_| | \__ \ | |_| |  ____) |  \ V  V /  | (_) | | (_) | | | |  __/
|______|  \__,_| |___/  \__, | |_____/    \_/\_/    \___/   \___/  |_|  \___|
                         __/ |                                                
                        |___/
do you want to release Index.php? [ Y / N (default) ] : Y
do you want to release Router.php? [ Y / N (default) ] : Y
install success,enjoy!!!
dont forget run composer dump-autoload !!!
```

> 注意，上述输入了 `Y` 、 `Y`，表示选择了 release(释放) `Index.php`、`Router.php` 和 `App` 目录在项目根目录，默认 `N` 、 `N` 是不释放的，也不会自动生成 `App` 目录和 `App` 命名空间。

::: danger
新版安装注意事项
:::

- 新版的 `EasySwoole` 安装默认不会提供 `App` 命名空间，还有 `Index` 控制器
- 重复安装时需要填写 `N`，不需要覆盖已经有的 `EasySwooleEvent.php、index.php、dev.php、produce.php` 等文件
- 当提示 `exec` 函数被禁用时，请自己手动执行 `composer dump-autoload` 命令更新命名空间
- 当提示 `symlink` 与 `readlink` 函数被禁用时，请自行修改 `php.ini` 配置文件取消这些函数的禁用

::: danger
重复安装时，会提示您是否选择 replace(覆盖) 如下文件 `Index.php`、`Router.php`、`dev.php`、`produce.php`、`bootstrap.php`、`EasySwooleEvent.php`，你可以根据需要选择是否覆盖(replace)，执行界面如下：
:::

```bash
php vendor/easyswoole/easyswoole/bin/easyswoole.php install
 ______                          _____                              _        
|  ____|                        / ____|                            | |       
| |__      __ _   ___   _   _  | (___   __      __   ___     ___   | |   ___ 
|  __|    / _` | / __| | | | |  \___ \  \ \ /\ / /  / _ \   / _ \  | |  / _ \
| |____  | (_| | \__ \ | |_| |  ____) |  \ V  V /  | (_) | | (_) | | | |  __/
|______|  \__,_| |___/  \__, | |_____/    \_/\_/    \___/   \___/  |_|  \___|
                         __/ |                                                
                        |___/                                                
Index.php has already existed, do you want to replace it? [ Y / N (default) ] : N
Router.php has already existed, do you want to replace it? [ Y / N (default) ] : N
dev.php has already existed, do you want to replace it? [ Y / N (default) ] : N
produce.php has already existed, do you want to replace it? [ Y / N (default) ] : N
bootstrap.php has already existed, do you want to replace it? [ Y / N (default) ] : N
EasySwooleEvent.php has already existed, do you want to replace it? [ Y / N (default) ] : N
install success,enjoy!!!
dont forget run composer dump-autoload !!!
```

::: warning
如果您使用 `Docker` 方式进行安装，您安装成功之后就可以在宿主机 `/var/www/` 中看到您安装好的代码了。 由于 `EasySwoole` 是持久化的 `CLI` 框架，当您修改完您的代码后，通过 `CTRL + C` 终止当前启动的进程实例，并重新执行 `php easyswoole.php server start` 启动命令即可。如果您想开启后台守护模式运行，用于部署，可查看 [基础管理命令](/QuickStart/command.md)
:::

## 安装报错
当执行安装脚本，出现类似以下错误时：
```
dir=$(cd "${0%[/\\]*}" > /dev/null; cd '../easyswoole/easyswoole/bin' && pwd)

if [ -d /proc/cygdrive ]; then
    case $(which php) in
        $(readlink -n /proc/cygdrive)/*)
            # We are in Cygwin using Windows php, so the path must be translated
            dir=$(cygpath -m "$dir");
            ;;
    esac
fi

"${dir}/easyswoole" "$@"
```

请检查环境是否为宝塔等其他集成面板，或者是 `php.ini` 配置项中禁用了 ```symlink``` 与 ```readlink``` 函数，如果禁用了，请关闭这两个函数的禁用，并删除 `vender 目录`，然后重新执行 ```composer require``` 或者是 ```composer install``` 或者是 ```composer update```。

如果取消了函数禁用并且删除 `vendor` 目录，并重新执行 `composer install` 之后。依旧出现以上错误时，大概率是因为虚拟机等权限原因导致软链接失效。可使用 `php vendor/easyswoole/easyswoole/bin/easyswoole.php` 命令进行启动框架。或者直接修改项目根目录的 `easyswoole` 文件，引入 `vendor/easyswoole/easyswoole/bin/easyswoole.php`。

## 安装 IDE 代码提示组件
> composer require easyswoole/swoole-ide-helper

## 启动框架

中途没有报错的话，执行：
```bash
# 启动框架
cd /var/www
php easyswoole.php server start
```
此时可以访问 `http://localhost:9501` 就看到框架的欢迎页面，表示框架已经安装成功。

### 启动框架失败可能遇到的问题
- not controller class match
   - `composer.json` 注册 `App` 这个名称空间了吗？
   - 执行过 ```composer dump-autoload``` 了吗？
   - 存在 `Index` 控制器，但是文件大小写、路径都对了吗？

- task socket listen fail
   - 注意，在部分环境下，例如 `win10` 的 `docker` 环境中，不可把虚拟机共享目录作为 `EasySwoole` 的 `Temp` 目录，否则会因为权限不足无法创建 `socket`，产生报错：`listen xxxxxx.sock fail`，为此可以手动在 `dev.php` 配置文件里把 `Temp` 目录改为其他路径即可，如：`'/tmp'`
   
- 外网无法正常访问
  - 注意：用户如果外网无法正常访问时，请检查机器环境的防火墙/安全组是否开放 `9501` 或其他对应端口。详细解决方案请看 [常见问题](/QuickStart/problem.md)
  

## 其他

- QQ 交流群
    - VIP 群 579434607 （本群需要付费599元）
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

     ![](/Images/authWx.png)
     
- [捐赠](/Preface/donate.md) 您的捐赠是对 `EasySwoole` 项目开发组最大的鼓励和支持。我们会坚持开发维护下去。 您的捐赠将被用于：
    - 持续和深入地开发
    - 文档和社区的建设和维护
    
<script>
        if(localStorage.getItem('isNew') != 1){
            localStorage.setItem('isNew',1);
            layer.confirm('是否给 EasySwoole 点个赞',{offset:'c'},function (index) {
                 layer.msg('感谢您的支持',{offset:'c'});
                     setTimeout(function () {
                         window.open('https://github.com/easy-swoole/easyswoole');
                  },1500);
             });              
        }
</script>
