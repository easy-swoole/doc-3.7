---
title: easyswoole基础使用-command
meta:
  - name: description
    content: easyswoole基础使用-command
  - name: keywords
    content: easyswoole基础使用-command
---
# 基本管理命令

## 框架安装

> php easyswoole.php install

## 服务管理

::: tip
  注意：以下命令只针对 `EasySwoole 3.7.x` 及以后版本，`EasySwoole 3.7.x` 之前版本管理命令请查看 [旧版本管理命令](https://github.com/easy-swoole/doc/blob/master/Cn/QuickStart/command.md) 
:::

`EasySwoole` 框架主命令。

可执行 `php easyswoole.php server -h` 来查看具体操作。

**服务启动**

> php easyswoole.php server start

**守护进程方式启动**

> php easyswoole.php server start -d

**指定配置文件启动服务**

默认为 `dev`，即 `-mode` 参数默认为 `dev`，即默认以项目根目录的 `dev.php` 作为框架运行的配置文件。

指定以项目根目录的 `produce.php` 作为框架运行的配置文件，请运行如下命令：

`-d` 可选参数：守护进程

> php easyswoole.php server start -mode=produce

**停止服务**

> php easyswoole.php server stop

**强制停止服务**

> php easyswoole.php server stop -force

**热重启**

仅会重启 `worker` 进程

> php easyswoole.php server reload

**重启服务**

`-d` 可选参数：守护进程

> php easyswoole.php server restart

**服务状态**

> php easyswoole.php server status

## 进程管理

`EasySwoole` 内置对于 `Process` 的命令行操作，方便开发者友好地去管理 `Process`。

可执行 `php easyswoole.php process -h` 来查看具体操作。

**显示所有进程**

> php easyswoole.php process show

**如果想要以 `MB` 形式显示：**

> php easyswoole.php process show -d

**杀死指定进程(PID)**

> php easyswoole.php process kill --pid=PID

**杀死指定进程组(GROUP)**

> php easyswoole.php process kill --group=GROUP_NAME

**杀死所有进程**

> php easyswoole.php process killAll

**强制杀死进程**

需要带上 `-f` 参数，例如：

> php easyswoole.php process kill --pid=PID -f


## Crontab 管理

`EasySwoole` 内置对于 `Crontab` 的命令行操作，方便开发者友好地去管理 `Crontab`。

可执行 `php easyswoole.php crontab -h` 来查看具体操作。

**查看所有注册的 Crontab**

> php easyswoole.php crontab show

**停止指定的 Crontab**

> php easyswoole.php crontab stop --name=TASK_NAME

**恢复指定的 Crontab**

> php easyswoole.php crontab resume --name=TASK_NAME

**立即跑一次指定的 Crontab**

> php easyswoole.php crontab run --name=TASK_NAME

## Task 管理

**查看 `Task` 进程状态**

> php easyswoole.php task status


## 单元测试

::: tip
 注意：需要先使用命令 `composer require easyswoole/phpunit` 安装单元测试组件包，然后才可以执行如下命令。详细使用请看 [单元测试](/Components/phpunit.md) 章节。
:::

**协程方式执行单元测试**

单元测试用例存放在项目根目录的 `tests` 目录。

> php easyswoole.php phpunit tests

**非协程方式执行单元测试**

单元测试用例存放在项目根目录的 `tests` 目录。

> php easyswoole.php phpunit tests --no-coroutine
