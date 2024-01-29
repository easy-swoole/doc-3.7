---
title: easyswoole框架部署-docker
meta:
  - name: description
    content: easyswoole框架部署-docker
  - name: keywords
    content: easyswoole框架部署-docker
---

# Docker部署

`Docker` 是一个开源的应用容器引擎，让开发者可以打包他们的应用以及依赖包到一个可移植的镜像中，然后发布到任何流行的 `Linux` 或 `Windows` 机器上，也可以实现虚拟化。容器是完全使用沙箱机制，相互之间不会有任何接口。

:::tip
使用 `Docker` 部署前，需要用户自行安装[Docker](https://www.docker.com/get-started)。
:::

## 部署前必看

部分机器(例如 `Docker` 环境)在使用框架时遇到类似 `DNS Lookup resolve failed...` 错误，请更换机器的 `DNS` 为阿里云公共 DNS `223.5.5.5` 和 `223.6.6.6`。具体更换步骤可查看 [更换 DNS](https://www.alidns.com/knowledge?type=SETTING_DOCS#user_linux)

## 启动容器
可以根据实际情况，映射到宿主机对应的目录，下面以宿主机目录 `/workspace/project` 为例

:::tip
如果 `docker` 启动时开启了 `selinux-enabled` 选项，容器内访问宿主机资源就会受限，所以启动容器时可以增加 `--privileged -u root` 选项
:::

```bash
docker run --name easyswoole \
-v /workspace/project:/var/www/project \
-p 9501:9501 -it \
--privileged -u root \
--entrypoint /bin/sh \
easyswoolexuesi2021/easyswoole:php8.1.22-alpine3.16-swoole4.8.13
```

上面利用 `Docker` 的映射功能，将宿主机目录 `/workspace/project` 映射到容器 `/var/www/project` 中。方便我们在宿主机开发，容器内进行同步测试。

你可以根据需要选择下面这些公共镜像：

- `easyswoolexuesi2021/easyswoole:php8.1.22-alpine3.16-swoole4.8.13`
- `easyswoolexuesi2021/easyswoole:php8.1.22-alpine3.16-swoole5.1.1`
- `easyswoolexuesi2021/easyswoole:php8.2.8-alpine3.18-swoole4.8.13`
- `easyswoolexuesi2021/easyswoole:php8.2.8-alpine3.18-swoole5.1.1`
- `easyswoolexuesi2021/easyswoole:php8.2.14-alpine3.19-swoole4.8.13`
- `easyswoolexuesi2021/easyswoole:php8.2.14-alpine3.19-swoole5.1.1`

上述镜像都是基于 `alpine` 系统制作的，是比较轻量级的，适合生产环境部署。后续我们还会考虑支持 `centos`、`ubuntu`、`debian` 等系统用于制作镜像，敬请期待。如果您想自定义镜像，可查看我们提供的 `dockerfiles` 仓库，国内 Gitea [easyswoole/docker](https://gitee.com/1592328848/easyswoole-docker)，Github [easyswoole/docker](https://github.com/XueSiLf/easyswoole-docker)。

### 创建项目

进入容器。

```bash
docker exec -it easyswoole bash
```

创建项目。

```bash
cd /var/www/project
composer require easyswoole/easyswoole
php vendor/bin/easyswoole.php install
# php vendor/bin/easyswoole install # 当你项目中的 EasySwoole 框架本低于 3.7.1 时
```

:::tip
注意，在部分环境下，例如 `Win10` 系统的 `docker` 环境。
不可把虚拟机共享目录作为 `EasySwoole` 的 `Temp` 目录，将会因为权限不足无法创建 `socket`。这将产生报错：`listen xxxxxx.sock fail`， 为此可以手动在 `dev.php` 配置文件里把 `Temp` 目录（`TEMP_DIR`配置项）改为其他路径即可，如：`'/tmp'`。
:::

### 启动项目

```bash
cd /var/www/project
php easyswoole.php server start
# php easyswoole.php server start # 当你项目中的 EasySwoole 框架本低于 3.7.1 时
```

接下来，就可以在宿主机 `/var/www/project` 中看到您安装好的代码了。 由于 `EasySwoole` 是持久化的 CLI 框架，当您修改完您的代码后，通过 `CTRL + C` 终止当前启动的进程实例，并重新执行 `php easyswoole.php server start` 启动命令即可。

上述命令执行完成后，宿主机浏览器访问 `http://127.0.0.1:9501/` 即可看到欢迎页。如果访问欢迎页遇到如下情形：`not controller class match`，请进入容器重新执行安装命令 `php easyswoole.php install`，并且输入 `Y`、`Y`，再次执行 `php easyswoole.php server start` 启动服务，就可以正常访问欢迎页了，详见 [框架安装](/QuickStart/install.md)。
