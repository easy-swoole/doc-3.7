---
title: easyswoole基础开发示例
meta:
- name: description
  content: easyswoole基础开发示例
- name: keywords
  content: easyswoole基础开发示例
---

# 基础开始示例

## demo 地址

基础开发示例已经开源，源码地址：https://github.com/XueSiLf/demo-3.7.x

::: danger
注意事项，请先看完这里，再往下继续浏览。因为下面的 `demo` 中使用到了 `php8` 的注解特性，所以您需要先学习注解如何使用，可查看 `php` 官方文档的 [注解](https://www.php.net/manual/zh/language.attributes.php) 文档。如果您已经对注解用法非常熟悉了，可直接往下继续浏览。
:::

## 安装

### 框架安装

#### Linux(Centos/Ubuntu/MacOS) 下安装

- 我们先把当前的 `php` 环境安装好 `swoole` 拓展，安装 `swoole 扩展` 步骤可查看 [安装 Swoole](/QuickStart/installSwoole.md) 章节，然后执行 `php --ri swoole` 确保可以看到 `swoole` 拓展版本为 `4.8.13`
- 建立一个目录，名为 `Test` ，执行 `composer require easyswoole/easyswoole=3.7.x` 引入 `easyswoole`
- 执行 `php vendor/bin/easyswoole.php install` 进行安装，然后输入 `Y`、`Y`

#### Docker 下安装

```bash
docker run --name easyswoole \
-v /tmp/easyswoole:/var/www \
-p 9501:9501 -it \
--privileged -u root \
--entrypoint /bin/sh \
easyswoolexuesi2021/easyswoole:php8.1.22-alpine3.16-swoole4.8.13

cd /var/www
mkdir Test
cd Test
composer require easyswoole/easyswoole=3.7.x
php vendor/bin/easyswoole.php install # 然后输入 `Y`、`Y`
```

### 组件引入

```bash
// 引入 IDE 代码提示组件
composer require swoole/ide-helper
```

### 命名空间注册

编辑 `Test` 根目录下的 `composer.json` 文件，如果自动加载中没有 `App` 命名空间，请在 `autoload.psr-4` 中加入 `"App\\": "App/"`，然后执行 `composer dumpautoload -o` 进行名称空间的更新。`composer.json` 文件大体结构如下：

```json
{
    "require": {
        "easyswoole/easyswoole": "3.7.x",
        "swoole/ide-helper": "^5.1"
    },
    "autoload": {
        "psr-4": {
            "App\\": "App/"
        }
    }
}
```

### 安装后目录结构

```
Test                    项目部署目录
├─App                     应用目录
│  ├─HttpController      控制器目录(如果没有，请自行创建)
├─Log                     日志文件目录（启动后创建）
├─Temp                    临时文件目录（启动后创建）
├─vendor                  第三方类库目录
├─bootstrap.php           框架 bootstrap 事件
├─composer.json           Composer 架构
├─composer.lock           Composer 锁定
├─EasySwooleEvent.php     框架全局事件
├─easyswoole.php          框架管理脚本
├─dev.php                 开发配置文件
├─produce.php             生产配置文件
```

## 连接池实现

### 配置项

创建配置文件 `Test/Config/DATABASE.php`，加入以下配置信息，**注意：请根据自己的 `mysql` 服务器信息填写账户密码**。

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link    https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact https://www.easyswoole.com/Preface/contact.html
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);
return [
    'DATABASE' => [
        // 添加 MySQL 及对应的连接池配置
        /*################ MYSQL CONFIG ##################*/
        'MYSQL' => [
            [
                'name'              => 'default', // 数据库连接池名称
                'useMysqli'         => false, // 是否是使用php-mysqli扩展
                'host'              => '127.0.0.1', // 数据库地址
                'port'              => 3306, // 数据库端口
                'user'              => 'easyswoole', // 数据库用户名
                'password'          => 'easyswoole', // 数据库用户密码
                'timeout'           => 45, // 数据库连接超时时间
                'charset'           => 'utf8', // 数据库字符编码
                'database'          => 'easyswoole_demo', // 数据库名
                'autoPing'          => 5, // 自动 ping 客户端链接的间隔
                'strict_type'       => false, // 不开启严格模式
                'fetch_mode'        => false,
                'returnCollection'  => false, // 设置返回结果为 数组
                // 配置 数据库 连接池配置，配置详细说明请看连接池组件 https://www.easyswoole.com/Components/Pool/introduction.html
                'intervalCheckTime' => 15 * 1000, // 设置 连接池定时器执行频率
                'maxIdleTime'       => 10, // 设置 连接池对象最大闲置时间 (秒)
                'maxObjectNum'      => 20, // 设置 连接池最大数量
                'minObjectNum'      => 5, // 设置 连接池最小数量
                'getObjectTimeout'  => 3.0, // 设置 获取连接池的超时时间
                'loadAverageTime'   => 0.001, // 设置负载阈值
            ]
        ]
    ]
];
```

修改 `Test/EasySwooleEvent.php` 文件，在 `initialize` 方法中添加如下内容，加载配置文件，

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link    https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact https://www.easyswoole.com/Preface/contact.html
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
 
namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        // 加载配置文件
        Config::getInstance()->loadDir(EASYSWOOLE_ROOT . '/Config');
    }
    
    // ...
} 
```

进行如上配置之后，我们需要在 `MySQL` 服务端创建一个名为 `easyswoole_demo` 的数据库，选择字符串编码为 `utf8mb4`，字符排序规则为 `utf8mb4_general_ci`。

### 引入数据库连接池组件 FastDb

执行以下命令用于实现数据库连接池组件 FastDb 库的引入。

```php
composer require easyswoole/fast-db=2.x
```

### 注册数据库连接池

编辑 `Test` 项目根目录下的 `EasySwooleEvent.php` 文件，在 `initialize` 或 `mainServerCreate` 事件函数中进行 `FastDb` 的连接池的注册，内容如下：

```php
<?php

namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\FastDb\FastDb;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
   
        // 加载配置文件
        Config::getInstance()->loadDir(EASYSWOOLE_ROOT . '/Config');

        ###### 注册 mysql orm 连接池 ######
        $mysqlConfigs = Config::getInstance()->getConf('DATABASE.MYSQL');
        foreach ($mysqlConfigs as $mysqlConfig) {
            $configObj = new \EasySwoole\FastDb\Config($mysqlConfig);
            // 【可选操作】我们已经在 DATABASE.php 中进行了配置
            # $configObj->setMaxObjectNum(20); // 配置连接池最大数量
            FastDb::getInstance()->addDb($configObj);
        }
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 或者 在此函数中注册 和上面等价
        ###### 注册 mysql orm 连接池 ######
        // $mysqlConfigs = Config::getInstance()->getConf('DATABASE.MYSQL');
        // foreach ($mysqlConfigs as $mysqlConfig) {
        //    $configObj = new \EasySwoole\FastDb\Config($mysqlConfig);
            // 【可选操作】我们已经在 DATABASE.php 中进行了配置
            # $configObj->setMaxObjectNum(20); // 配置连接池最大数量
        //    FastDb::getInstance()->addDb($configObj);
        // }
    }
}
```

::: warning
在 `initialize` 事件中注册数据库连接池，使用这个 `$config` 可同时配置连接池大小等。
具体查看 [FastDb 组件章节](/Components/FastDb/install.md) 的使用。
:::

## 模型定义

### 管理员模型

#### 新增管理员用户表

在 `easyswoole_demo` 数据库中执行如下 `sql` 脚本，创建管理员用户表 `admin_list`。

```sql
DROP TABLE IF EXISTS `admin_list`;
CREATE TABLE `admin_list` (
  `adminId` int NOT NULL AUTO_INCREMENT,
  `adminName` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adminAccount` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adminPassword` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adminSession` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adminLastLoginTime` int DEFAULT NULL,
  `adminLastLoginIp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`adminId`) USING BTREE,
  UNIQUE KEY `adminAccount` (`adminAccount`) USING BTREE,
  KEY `adminSession` (`adminSession`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin_list` VALUES (1, 'EasySwoole', 'easyswoole', 'e10adc3949ba59abbe56e057f20f883e', '', 1700891404, '127.0.0.1');
```

#### 新增 model 文件

新建 `App/Model/Admin/AdminModel.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\Model\Admin;

use App\Model\BaseModel;
use EasySwoole\FastDb\Attributes\Property;
use EasySwoole\FastDb\Beans\Query;

/**
 * Class AdminModel
 *
 * @property int    $adminId
 * @property string $adminName
 * @property string $adminAccount
 * @property string $adminPassword
 * @property string $adminSession
 * @property int    $adminLastLoginTime
 * @property string $adminLastLoginIp
 */
class AdminModel extends BaseModel
{
    #[Property(isPrimaryKey: true)]
    public int $adminId;
    #[Property]
    public string $adminName;
    #[Property]
    public string $adminAccount;
    #[Property]
    public string $adminPassword;
    #[Property]
    public string $adminSession;
    #[Property]
    public int $adminLastLoginTime;
    #[Property]
    public string $adminLastLoginIp;

    protected string $primaryKey = 'adminId';
    protected string $table = 'admin_list';

    /**
     * @getAll
     *
     * @param int         $page
     * @param null|string $keyword
     * @param int         $pageSize
     *
     * @return array[$total, $list]
     */
    public function getAll(int $page = 1, ?string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['adminAccount'] = ['%' . $keyword . '%', 'like'];
        }

        $this->queryLimit()->page($page, true, $pageSize)
            ->orderBy($this->primaryKey, 'DESC');

        /** \EasySwoole\FastDb\Beans\ListResult $resultList */
        $resultList = $this->where($where)->all();

        $total = $resultList->totalCount();
        $list = $resultList->list();

        return ['total' => $total, 'list' => $list];
    }

    /**
     * 登录成功后请返回更新后的bean
     */
    public function login(): ?AdminModel
    {
        $where = [
            'adminAccount'  => $this->adminAccount,
            'adminPassword' => $this->adminPassword
        ];
        return self::findRecord($where);
    }

    /**
     * 以account进行查询
     */
    public function accountExist(array $field = ['*']): ?AdminModel
    {
        return self::findRecord(function (Query $query) use ($field) {
            $query->fields($field)
                ->where('adminAccount', $this->adminAccount);
        });
    }

    public function getOneBySession(array $field = ['*']): ?AdminModel
    {
        $this->queryLimit()->fields($field);
        $this->where(['adminSession' => $this->adminSession]);
        return $this->find();
    }

    public function logout()
    {
        $where = [$this->primaryKey => $this->adminId];
        $update = ['adminSession' => ''];
        return self::fastUpdate($where, $update);
    }
}
```

针对上述类似 `: ?AdminModel`，不懂这种函数返回值类型声明的同学，请查看 [函数返回值类型声明](https://www.php.net/manual/zh/migration70.new-features.php)，属于 `PHP 7` 的新特性。

::: warning
关于 `Model` 的定义可查看 [FastDb 模型定义章节](/Components/FastDb/definitionModel.md)。
:::

::: warning
关于 `IDE` 自动提示，只要你在类上面注释中加上 `@property $adminId`，`IDE` 就可以自动提示类的这个属性。
:::


### 普通用户模型

普通用户模型和管理员模型同理。

#### 建表

在数据库中执行如下 `sql` 脚本，创建普通用户表 `user_list`。

```sql
DROP TABLE IF EXISTS `user_list`;
CREATE TABLE `user_list` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `userName` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `userAccount` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `userPassword` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `addTime` int unsigned DEFAULT '0',
  `lastLoginIp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastLoginTime` int unsigned DEFAULT '0',
  `userSession` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `state` tinyint unsigned DEFAULT '0',
  `money` int unsigned NOT NULL DEFAULT '0' COMMENT '用户余额',
  `frozenMoney` int unsigned NOT NULL DEFAULT '0' COMMENT '冻结余额',
  PRIMARY KEY (`userId`) USING BTREE,
  UNIQUE KEY `pk_userAccount` (`userAccount`) USING BTREE,
  KEY `userSession` (`userSession`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user_list` VALUES (1, 'easyswoole', 'easyswoole', 'e10adc3949ba59abbe56e057f20f883e', '18888888888', 0, '127.0.0.1', 1700892578, '', 0, 0, 0);
```

#### 新增 model 文件

新建 `App/Model/User/UserModel.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\Model\User;

use App\Model\BaseModel;
use EasySwoole\FastDb\Attributes\Property;

/**
 * Class UserModel
 *
 * @property int    $userId
 * @property string $userName
 * @property string $userAccount
 * @property string $userPassword
 * @property string $phone
 * @property int    $addTime
 * @property string $lastLoginIp
 * @property int    $lastLoginTime
 * @property string $userSession
 * @property int    $state
 * @property int    $money
 * @property int    $frozenMoney
 */
class UserModel extends BaseModel
{
    protected string $table = 'user_list';
    protected string $primaryKey = 'userId';

    public const STATE_PROHIBIT = 0; // 禁用状态
    public const STATE_NORMAL = 1; // 正常状态

    #[Property(isPrimaryKey: true)]
    public int $userId;
    #[Property]
    public string $userName;
    #[Property]
    public string $userAccount;
    #[Property]
    public string $userPassword;
    #[Property]
    public string $phone;
    #[Property]
    public int $addTime;
    #[Property]
    public ?string $lastLoginIp;
    #[Property]
    public ?int $lastLoginTime;
    #[Property]
    public ?string $userSession;
    #[Property]
    public int $state;
    #[Property]
    public int $money;
    #[Property]
    public int $frozenMoney;

    /**
     * @getAll
     *
     * @param int         $page
     * @param string|null $keyword
     * @param int         $pageSize
     *
     * @return array[total,list]
     */
    public function getAll(int $page = 1, ?string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['userAccount'] = ['%' . $keyword . '%', 'like'];
        }

        $this->queryLimit()->page($page, withTotalCount: true, pageSize: $pageSize)
            ->orderBy($this->primaryKey, 'DESC');
        /** \EasySwoole\FastDb\Beans\ListResult $resultList */
        $resultList = $this
            ->where($where)
            ->all();

        $total = $resultList->totalCount();
        $list = $resultList->list();

        return ['total' => $total, 'list' => $list];
    }

    public function getOneByPhone(array $field = ['*']): ?UserModel
    {
        $this->queryLimit()->fields($field);
        return $this->find(['phone' => $this->phone]);
    }

    /*
    * 登录成功后请返回更新后的bean
    */
    public function login(): ?UserModel
    {
        return $this->find([
            'userAccount'  => $this->userAccount,
            'userPassword' => $this->userPassword
        ]);
    }

    public function getOneBySession(array $field = ['*']): ?UserModel
    {
        $this->queryLimit()->fields($field);
        return $this->find(['userSession' => $this->userSession]);
    }

    public function logout()
    {
        return $this->where([$this->primaryKey => $this->userId])->updateWithLimit(['userSession' => '']);
    }
}
```

### banner 模型

#### 建表

在数据中执行如下 `sql` 脚本，创建 `banner` 表 `banner_list`。

```sql
DROP TABLE IF EXISTS `banner_list`;
CREATE TABLE `banner_list` (
  `bannerId` int NOT NULL AUTO_INCREMENT,
  `bannerName` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bannerImg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'banner图片',
  `bannerDescription` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bannerUrl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转地址',
  `state` tinyint DEFAULT NULL COMMENT '状态0隐藏 1正常',
  PRIMARY KEY (`bannerId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `banner_list` VALUES (1, '测试banner', 'asdadsasdasd.jpg', '测试的banner数据', 'www.easyswoole.com', 1);
```

#### 新增 model 文件

新建 `App/Model/Admin/BannerModel.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\Model\Admin;

use App\Model\BaseModel;
use EasySwoole\FastDb\Attributes\Property;

/**
 * Class BannerModel
 *
 * @property int    $bannerId
 * @property string $bannerName
 * @property string $bannerImg
 * @property string $bannerDescription
 * @property string $bannerUrl
 * @property int    $state
 */
class BannerModel extends BaseModel
{
    protected string $table = 'banner_list';
    protected string $primaryKey = 'bannerId';

    #[Property(isPrimaryKey: true)]
    public int $bannerId;
    #[Property]
    public string $bannerName;
    #[Property]
    public string $bannerImg;
    #[Property]
    public string $bannerDescription;
    #[Property]
    public string $bannerUrl;
    #[Property]
    public int $state;

    public function getAll(int $page = 1, int $state = 1, ?string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['bannerUrl'] = ['%' . $keyword . '%', 'like'];
        }

        $where['state'] = $state;

        $this->queryLimit()->page($page, withTotalCount: true, pageSize: $pageSize)
            ->orderBy($this->primaryKey, 'DESC');
        /** \EasySwoole\FastDb\Beans\ListResult $resultList */
        $listResult = $this->where($where)->all();
        $total = $listResult->totalCount();
        $list = $listResult->list();

        return ['total' => $total, 'list' => $list];
    }
}
```

## 控制器定义

### 全局基础控制器定义

新建 `App/Httpcontroller/Base.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\HttpAnnotation\AnnotationController;

class Base extends AnnotationController
{
    public function index(): void
    {
        $this->actionNotFound('index');
    }

    /**
     * 获取用户的真实IP
     *
     * @param string $headerName 代理服务器传递的标头名称
     *
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

    protected function input(string $name, mixed $default = null)
    {
        $value = $this->request()->getRequestParam($name);
        return $value ?? $default;
    }
}
```

::: warning
上述新增的基础控制器 (Base.php) 里面的方法用于获取用户 `ip`，以及获取 `api` 参数。
:::

::: warning
上述新增的基础控制器 (Base.php) 继承了 `\EasySwoole\HttpAnnotation\AnnotationController` ，这个是注解支持控制器，具体使用可查看 [注解控制器章节](/HttpServer/AnnotationController/install.md)
:::

### api 基础控制器定义

新建 `App/Httpcontroller/Api/ApiBase.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api;

use App\HttpController\Base;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;

abstract class ApiBase extends Base
{
    public function index(): void
    {
        $this->actionNotFound('index');
    }

    protected function actionNotFound(?string $action): void
    {
        $this->writeJson(Status::CODE_NOT_FOUND);
    }

    public function onRequest(?string $action): ?bool
    {
        if (!parent::onRequest($action)) {
            return false;
        }
        return true;
    }

    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ValidateFail) {
            $this->writeJson(400, null, $throwable->getMessage());
        } else {
            if (Core::getInstance()->runMode() === 'dev') {
                $this->writeJson(500, null, $throwable->getMessage());
            } else {
                Trigger::getInstance()->throwable($throwable);
                $this->writeJson(500, null, '系统内部错误，请稍后重试');
            }
        }
    }
}
```

::: warning
上述 `api` 基类控制器 (ApiBase.php)，用于拦截注解异常，以及 `api` 异常时给用户返回一个 `json` 格式错误信息。
:::

### 公共基础控制器定义

新建 `App/Httpcontroller/Api/Common/CommonBase.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api\Common;

use App\HttpController\Api\ApiBase;

class CommonBase extends ApiBase
{

}
```

### 公共控制器

公共控制器放不需要登录即可查看的控制器，例如 `banner` 列表查看：

新增 `App/HttpController/Api/Common/Banner.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api\Common;

use App\Model\Admin\BannerModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\Required;

class Banner extends CommonBase
{
    #[Api(
        apiName: 'bannerGetOne',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/common/banner/getOne',
        requestParam: [
        new Param(name: 'bannerId', from: ParamFrom::GET, validate: [
            new Required(),
            new Integer(),
        ], description: new Description('主键id')),
    ],
        description: 'getOne'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:20
     */
    public function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new BannerModel();
        $bean = $model->find((int)$param['bannerId']);
        if ($bean) {
            $this->writeJson(Status::CODE_OK, $bean, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }
    }

    #[Api(
        apiName: 'bannerGetAll',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/common/banner/getAll',
        requestParam: [
        new Param(name: 'page', from: ParamFrom::GET, validate: [
            new Optional(),
            new Integer(),
        ], description: new Description('页数')),
        new Param(name: 'limit', from: ParamFrom::GET, validate: [
            new Optional(),
            new Integer(),
        ], description: new Description('每页总数')),
        new Param(name: 'keyword', from: ParamFrom::GET, validate: [
            new Optional(),
            new MaxLength(32),
        ], description: new Description('关键字')),
    ],
        description: 'getAll'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:20
     */
    public function getAll()
    {
        $param = $this->request()->getRequestParam();
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $model = new BannerModel();
        $data = $model->getAll($page, 1, $param['keyword'] ?? null, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }
}
```

::: warning
注意：可以看到，在上文 `getAll` 方法中，有个特殊的写法 `requestParam: [new Param(name: 'page', from: ParamFrom::GET, validate: [new Optional(), new Integer(),], description: new Description('页数')),` 这是 `php8` 支持的注解写法，类似 `Java` 语言的注解，当使用这个注解之后，将会约束 `page` 参数必须是 `int`，具体的验证机制可查看 [`validate` 验证器 章节](/Components/Validate/validate.md)。框架中如何使用注解请查看 [注解控制器章节](/HttpServer/AnnotationController/install.md)
:::

::: warning
使用 `php easyswoole.php server start` 命令启动框架服务之后，访问链接：`http://localhost:9501/api/common/banner/getAll` (示例访问地址) 即可看到如下结果：`{"code":200,"result":{"total":1,"list":[{"bannerId":1,"bannerName":"测试banner","bannerImg":"asdadsasdasd.jpg","bannerDescription":"测试的banner数据","bannerUrl":"www.easyswoole.com","state":1}]},"msg":"success"}` (需要有数据才能看到具体输出)。
:::

### 管理员基础控制器定义

新建 `App/HttpController/Api/Admin/AdminBase.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api\Admin;

use App\Model\Admin\AdminModel;
use App\HttpController\Api\ApiBase;
use EasySwoole\Http\Message\Status;

class AdminBase extends ApiBase
{
    // public 才会根据协程清除
    public ?AdminModel $who;

    // session 的 cookie头
    protected string $sessionKey = 'adminSession';

    // 白名单
    protected array $whiteList = [];

    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:28
     */
    public function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            // 白名单判断
            if (in_array($action, $this->whiteList)) {
                return true;
            }

            // 获取登录信息
            if (!$this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登录已过期');
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:28
     */
    protected function getWho(): ?AdminModel
    {
        if (isset($this->who) && $this->who instanceof AdminModel) {
            return $this->who;
        }

        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }

        if (empty($sessionKey)) {
            return null;
        }

        $adminModel = new AdminModel();
        $adminModel->adminSession = $sessionKey;
        $this->who = $adminModel->getOneBySession();

        return $this->who;
    }
}
```

### 管理员登录控制器

新建 `App/HttpController/Api/Admin/Auth.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api\Admin;

use App\Model\Admin\AdminModel;
use EasySwoole\FastDb\Exception\RuntimeError;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\Required;

class Auth extends AdminBase
{
    protected array $whiteList = ['login'];

    /**
     * @return void
     * @throws RuntimeError
     * @throws Annotation
     */
    #[Api(
        apiName: 'login',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/auth/login',
        requestParam: [
        new Param(name: 'account', from: ParamFrom::GET, validate: [
            new Required(),
            new MaxLength(20)
        ], description: new Description('帐号')),
        new Param(name: 'password', from: ParamFrom::GET, validate: [
            new Required(),
            new MinLength(6),
            new MaxLength(16),
        ], description: new Description('密码')),
    ],
        description: '登陆,参数验证注解写法'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:31
     */
    public function login()
    {
        $param = $this->request()->getRequestParam();
        $model = new AdminModel();
        $model->adminAccount = $param['account'];
        $model->adminPassword = md5($param['password']);

        if ($user = $model->login()) {
            $sessionHash = md5(time() . $user->adminId);
            $user->updateWithLimit([
                'adminLastLoginTime' => time(),
                'adminLastLoginIp'   => $this->clientRealIP(),
                'adminSession'       => $sessionHash
            ]);

            $rs = $user->toArray();
            unset($rs['adminPassword']);
            $rs['adminSession'] = $sessionHash;
            $this->response()->setCookie('adminSession', $sessionHash, time() + 3600, '/');
            $this->writeJson(Status::CODE_OK, $rs, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '密码错误');
        }
    }

    #[Api(
        apiName: 'logout',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/auth/logout',
        requestParam: [
        new Param(name: 'adminSession', from: ParamFrom::COOKIE, validate: [
            new Required(),
        ], description: new Description('帐号')),
    ],
        description: '退出登录,参数注解写法'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:32
     *
     * @throws Annotation
     */
    public function logout()
    {
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams('adminSession');
        }

        if (empty($sessionKey)) {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', '尚未登录');
            return false;
        }

        $result = $this->getWho()->logout();
        if ($result) {
            $this->writeJson(Status::CODE_OK, '', '退出登录成功');
        } else {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', 'fail');
        }
    }

    #[Api(
        apiName: 'getInfo',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/auth/getInfo',
        description: '获取管理员信息'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:34
     */
    public function getInfo()
    {
        $this->writeJson(200, $this->getWho()->toArray(), 'success');
    }
}
```

::: warning
使用 `php easyswoole.php server start` 命令启动框架服务之后，访问链接：`http://localhost:9501/api/admin/auth/login?account=easyswoole&password=123456` (示例访问地址) 即可返回如下结果：``
:::

```json
{
  "code": 200,
  "result": {
    "adminId": 1,
    "adminName": "EasySwoole",
    "adminAccount": "easyswoole",
    "adminSession": "7262e8188ae9885e27e092538c08ca16",
    "adminLastLoginTime": 1706271125,
    "adminLastLoginIp": "127.0.0.1"
  },
  "msg": "success"
}
```

### 管理员用户管理控制器

新增 `App/httpController/Api/Admin/User.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api\Admin;

use App\Model\User\UserModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\InArray;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\IsPhoneNumber;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\Required;

class User extends AdminBase
{
    #[Api(
        apiName: 'userGetAll',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/user/getAll',
        requestParam: [
        new Param(name: 'page', from: ParamFrom::GET, validate: [
            new Optional(),
            new Integer(),
        ], description: new Description('页数')),
        new Param(name: 'limit', from: ParamFrom::GET, validate: [
            new Optional(),
            new Integer(),
        ], description: new Description('每页总数')),
        new Param(name: 'keyword', from: ParamFrom::GET, validate: [
            new Optional(),
            new MaxLength(32),
        ], description: new Description('关键字')),
    ],
        description: 'getAll'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:38
     */
    public function getAll()
    {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $model = new UserModel();
        $data = $model->getAll($page, $this->input('keyword'), $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }

    #[Api(
        apiName: 'userGetOne',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/user/getOne',
        requestParam: [
        new Param(name: 'userId', from: ParamFrom::GET, validate: [
            new Required(),
            new Integer(),
        ], description: new Description('户id')),
    ],
        description: 'getAll'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:40
     */
    public function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $rs = $model->find((int)$param['userId']);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }
    }

    #[Api(
        apiName: 'addUser',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/user/add',
        requestParam: [
        new Param(name: 'userName', from: ParamFrom::GET, validate: [
            new Optional(),
            new MaxLength(32),
        ], description: new Description('用户昵称')),
        new Param(name: 'userAccount', from: ParamFrom::GET, validate: [
            new Required(),
            new MaxLength(32),
        ], description: new Description('用户名')),
        new Param(name: 'userPassword', from: ParamFrom::GET, validate: [
            new Required(),
            new MinLength(6),
            new MaxLength(18),
        ], description: new Description('用户密码')),
        new Param(name: 'phone', from: ParamFrom::GET, validate: [
            new Optional(),
            new IsPhoneNumber(),
        ], description: new Description('手机号码')),
        new Param(name: 'state', from: ParamFrom::GET, validate: [
            new Optional(),
            new InArray([0, 1]),
        ], description: new Description('用户状态')),
    ],
        description: 'add'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:44
     */
    public function add()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel($param);
        $model->userPassword = md5($param['userPassword']);
        $rs = $model->insert();
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'add fail');
        }
    }

    #[Api(
        apiName: 'updateUser',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/user/update',
        requestParam: [
        new Param(name: 'userId', from: ParamFrom::GET, validate: [
            new Required(),
            new Integer(),
        ], description: new Description('用户id')),
        new Param(name: 'userPassword', from: ParamFrom::GET, validate: [
            new Optional(),
            new MinLength(6),
            new MaxLength(18),
        ], description: new Description('会员密码')),
        new Param(name: 'userName', from: ParamFrom::GET, validate: [
            new Optional(),
            new MaxLength(32),
        ], description: new Description('会员名')),
        new Param(name: 'state', from: ParamFrom::GET, validate: [
            new Optional(),
            new InArray([0, 1]),
        ], description: new Description('状态')),
        new Param(name: 'phone', from: ParamFrom::GET, validate: [
            new Optional(),
            new IsPhoneNumber(),
        ], description: new Description('手机号')),
    ],
        description: 'update'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:50
     */
    public function update()
    {
        $model = new UserModel();
        $userId = $this->input('userId');
        /**
         * @var $userInfo UserModel
         */
        $userInfo = $model->find((int)$userId);
        if (!$userInfo) {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '未找到该会员');
            return false;
        }

        $password = $this->input('userPassword');
        $update = [
            'userName'     => $this->input('userName', $userInfo->userName),
            'userPassword' => $password ? md5($password) : $userInfo->userPassword,
            'state'        => $this->input('state', $userInfo->state),
            'phone'        => $this->input('phone', $userInfo->phone),
        ];

        $rs = $userInfo->updateWithLimit($update);
        if ($rs === 0 || $rs === 1) {
            $this->writeJson(Status::CODE_OK, $rs, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'update fail');
        }
    }

    #[Api(
        apiName: 'deleteUser',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/admin/user/delete',
        requestParam: [
        new Param(name: 'userId', from: ParamFrom::GET, validate: [
            new Required(),
            new Integer(),
        ], description: new Description('用户id')),
    ],
        description: 'delete'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:55
     */
    public function delete()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel(['userId' => $param['userId']]);
        $rs = $model->delete();
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '删除失败');
        }
    }
}
```

::: warning
后台管理员登录之后，可通过此文件的接口，去进行会员的增删改查操作 (即 CURD)。
:::

::: warning
请求地址为：(示例访问地址) `http://127.0.0.1:9501/Api/Admin/User/getAll` (等方法)
:::

### 普通用户基础控制器定义

新增 `App/HttpController/Api/User/UserBase.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api\User;

use App\Model\User\UserModel;
use App\HttpController\Api\ApiBase;
use EasySwoole\Http\Message\Status;

class UserBase extends ApiBase
{
    protected ?UserModel $who;

    // session 的 cookie 头
    protected string $sessionKey = 'userSession';

    // 白名单
    protected array $whiteList = ['login', 'register'];

    public function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            // 白名单判断
            if (in_array($action, $this->whiteList)) {
                return true;
            }

            // 获取登录信息
            if (!$data = $this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登录已过期');
                return false;
            }

            // 刷新 cookie 存活
            $this->response()->setCookie($this->sessionKey, $data->userSession, time() + 3600, '/');

            return true;
        }

        return false;
    }

    public function getWho(): ?UserModel
    {
        if (isset($this->who) && $this->who instanceof UserModel) {
            return $this->who;
        }

        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }

        if (empty($sessionKey)) {
            return null;
        }

        $userModel = new UserModel();
        $userModel->userSession = $sessionKey;
        $this->who = $userModel->getOneBySession();

        return $this->who;
    }
}
```

### 普通用户登录控制器

新增 `App/HttpController/Api/User/Auth.php` 文件，编辑内容如下：

```php
<?php
/**
 * This file is part of EasySwoole.
 *
 * @link     https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact  https://www.easyswoole.com/Preface/contact.html
 * @license  https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */
declare(strict_types=1);

namespace App\HttpController\Api\User;

use App\Model\User\UserModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\Required;

class Auth extends UserBase
{
    protected array $whiteList = ['login'];

    #[Api(
        apiName: 'login',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/user/auth/login',
        requestParam: [
        new Param(name: 'userAccount', from: ParamFrom::GET, validate: [
            new Required(),
            new MaxLength(32)
        ], description: new Description('用户名')),
        new Param(name: 'userPassword', from: ParamFrom::GET, validate: [
            new Required(),
            new MinLength(6),
            new MaxLength(18),
        ], description: new Description('密码')),
    ],
        description: 'login'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 00:31
     */
    public function login()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $model->userAccount = $param['userAccount'];
        $model->userPassword = md5($param['userPassword']);

        if ($userInfo = $model->login()) {
            $sessionHash = md5(time() . $userInfo->userId);
            $userInfo->updateWithLimit([
                'lastLoginIp'   => $this->clientRealIP(),
                'lastLoginTime' => time(),
                'userSession'   => $sessionHash
            ]);
            $rs = $userInfo->toArray();
            unset($rs['userPassword']);
            $rs['userSession'] = $sessionHash;
            $this->response()->setCookie('userSession', $sessionHash, time() + 3600, '/');
            $this->writeJson(Status::CODE_OK, $rs);
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '密码错误');
        }
    }

    #[Api(
        apiName: 'logout',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/user/auth/logout',
        requestParam: [
        new Param(name: 'userSession', from: ParamFrom::GET, validate: [
            new Optional(),
        ], description: new Description('用户会话')),
    ],
        description: 'logout'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 01:07
     */
    public function logout()
    {
        $sessionKey = $this->request()->getRequestParam('userSession');
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams('userSession');
        }

        if (empty($sessionKey)) {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', '尚未登录');
            return false;
        }

        $result = $this->getWho()->logout();
        if ($result) {
            $this->writeJson(Status::CODE_OK, '', '退出登录成功');
        } else {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', 'fail');
        }
    }

    #[Api(
        apiName: 'getInfo',
        allowMethod: HttpMethod::GET,
        requestPath: '/api/user/auth/getInfo',
        description: 'getInfo'
    )]
    /**
     * Author: XueSi <hui.huang8540@gmail.com>
     * Time: 01:10
     */
    public function getInfo()
    {
        $this->writeJson(200, $this->getWho(), 'success');
    }
}
```

访问 `http://localhost:9501/api/user/auth/login?userAccount=easyswoole&userPassword=456789` 即可登录成功。

## 接口访问

上述 `demo` 中的所有接口均使用 `GET` 请求，可以在启动 `easyswoole` 服务后可使用浏览器访问如下 `URL` 进行体验：

```bash
# Admin 管理员模块
## auth 模块
- login 登录
  - http://localhost:9501/api/admin/auth/login?account=easyswoole&password=123456

- logout
  - http://localhost:9501/api/admin/auth/logout

- getInfo
  - http://localhost:9501/api/admin/auth/getInfo

## user manager 会员管理模块
- get all user
  - http://localhost:9501/api/admin/user/getAll
  - http://localhost:9501/api/admin/user/getAll?page=1&limit=2
  - http://localhost:9501/api/admin/user/getAll?keyword=easyswoole

- get one user
  - http://localhost:9501/api/admin/user/getOne?userId=1

- add user
  - http://localhost:9501/api/admin/user/add?userName=EasySwoole1&userAccount=easyswoole1&userPassword=123456

- update user
  - http://localhost:9501/api/admin/user/update?userId=1&userPassword=456789&userName=easyswoole&state=0&phone=18888888889

- delete user
  - http://localhost:9501/api/admin/user/delete?userId=2

# Common 公共模块
## banner 模块
- get one banner 读取一条banner
  - http://localhost:9501/api/common/banner/getOne?bannerId=1

- get all banner
  - http://localhost:9501/api/common/banner/getAll
  - http://localhost:9501/api/common/banner/getAll?page=1&limit=2
  - http://localhost:9501/api/common/banner/getAll?keyword=easyswoole

# User 会员模块
- user login
  - http://localhost:9501/api/user/auth/login?userAccount=easyswoole&userPassword=456789

- get user info
  - http://localhost:9501/api/user/auth/getInfo

- logout
  - http://localhost:9501/api/user/auth/logout
```
