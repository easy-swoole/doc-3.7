---
title: easyswoole框架安装环境要求
meta:
  - name: description
    content: easyswoole框架安装环境要求
  - name: keywords
    content: swoole 框架环境要求|easyswoole框架安装环境要求
---

# 环境要求

满足基本的环境要求才能运行框架，`EasySwoole` 框架对环境的要求十分简单，只需要满足运行 `Swoole` 扩展的条件，并且 `PHP` 版本在 8.1 以上即可

- [GitHub](https://github.com/easy-swoole/easyswoole)  喜欢记得点个 ***star***
- [Github for Doc](https://github.com/easy-swoole/doc-3.7) 文档贡献
- [Github for Doc 3.5.x](https://github.com/easy-swoole/doc) 3.5.x 版本文档

## 基础运行环境
- 保证 **PHP** 版本大于等于 **8.1**
- 保证 **Swoole PHP** 拓展版本大于等于 **4.8.13**
- 需要 **Pcntl PHP** 拓展的任意版本 
- 可能需要 **OpenSSL PHP** 扩展的任意版本（如需要使用到 HTTPS）
- 使用 **Linux** / **FreeBSD** / **MacOS** 这三类操作系统
- 使用 **Composer** 作为依赖管理工具

::: warning 
 参考下面的建议，它们都不是必须的，但是有助于更高效的使用框架和进行开发
:::

- 使用 **Ubuntu14** / **CentOS 7.0** 或更高版本操作系统
- 使用 **Docker** 

## Windows 环境下开发特殊说明

如果您想在 `Windows` 环境下进行开发，您可以通过 `Docker for Windows` 或 `WSL` 或 `虚拟机` 来作为运行环境。

### Windows 下使用 Docker 

具体如何安装 `Docker`，请自行`Google`查询资料进行安装。

`EasySwoole` 官方为您提供了满足各种需求的 `Docker` 镜像，[easyswoole/docker](https://github.com/XueSiLf/easyswoole-docker) 项目内已经为您准备好了各种版本的 `Dockerfile` ，或直接基于已经构建好的 [easyswoolexuesi2021/easyswoole](https://hub.docker.com/r/easyswoolexuesi2021/easyswoole) 镜像来运行。

### Windows 下使用 WSL2 (Windows Subsystem for Linux)

具体如何安装 `Windows Subsystem for Linux`，请自行`Google`查询资料进行安装。

### Windows 下使用 虚拟机

你还可以通过安装虚拟机的方式来模拟 `Linux` 环境，用该环境进行 `EasySwoole` 项目的开发。具体如何安装虚拟机，请自行`Google`查询资料进行安装。

## 其他

- QQ 交流群
    - VIP群 579434607 （本群需要付费599元）
    - EasySwoole 官方一群 633921431(已满)
    - EasySwoole 官方二群 709134628(已满)
    - EasySwoole 官方三群 932625047(已满)
    - EasySwoole 官方四群 779897753(已满)
    - EasySwoole 官方五群 853946743(已满)
    - EasySwoole 官方六群 524475224(已满)
    - EasySwoole 官方七群 1016674948
    
- 商业支持：
    - QQ 291323003
    - EMAIL admin@fosuss.com
        
- 作者微信

  <img src="/Images/authWx.jpg" width="220">

- [捐赠](/Preface/donate.md) 您的捐赠是对 `EasySwoole` 项目开发组最大的鼓励和支持。我们会坚持开发维护下去。 您的捐赠将被用于：
  - 持续和深入地开发
  - 文档和社区的建设和维护

- `EasySwoole` 的文档使用 `EasySwoole 框架` 提供服务，采用 `MarkDown 格式` 和自定义格式编写，若您在使用过程中，发现文档有需要纠正 / 补充的地方，请 `fork` 项目的文档仓库，进行修改补充，提交 `Pull Request` 并联系我们。
