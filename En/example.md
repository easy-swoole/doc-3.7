# EasySwoole

[![Latest Stable Version](https://poser.pugx.org/easyswoole/easyswoole/v/stable)](https://packagist.org/packages/easyswoole/easyswoole)
[![Total Downloads](https://poser.pugx.org/easyswoole/easyswoole/downloads)](https://packagist.org/packages/easyswoole/easyswoole)
[![Latest Unstable Version](https://poser.pugx.org/easyswoole/easyswoole/v/unstable)](https://packagist.org/packages/easyswoole/easyswoole)
[![License](https://poser.pugx.org/easyswoole/easyswoole/license)](https://packagist.org/packages/easyswoole/easyswoole)
[![Monthly Downloads](https://poser.pugx.org/easyswoole/easyswoole/d/monthly)](https://packagist.org/packages/easyswoole/easyswoole)



EasySwoole is a distributed, persistent memory PHP framework based on the Swoole extension. It was created specifically for APIs to get rid of the performance penalties associated with process calls and file loading. EasySwoole highly encapsulates the Swoole Server and still maintains the original features of the Swoole server, supports simultaneous monitoring of HTTP, custom TCP, and UDP protocols, allowing developers to write multi-process, asynchronous, and highly available applications with minimal learning cost and effort.
          
- Base on Swoole extension
- Built-in HTTP, TCP, WebSocket,Udp Coroutine Server
- Global dependency injection container
- PSR-7 based HTTP message implementation
- HTTP,TCP, WebSocket, Udp middleware support
- Scalable high performance RPC
- Database ORM
- Mysql, Redis, RPC, HTTP Coroutine Clients
- Coroutine and asynchronous task delivery
- Custom user processes
- RESTful supported
- High performance router
- Fast and flexible parameter validator
- Powerful log component
- Universal connection pools
- Remote Console support
- Crontab Rule Timer support

## ab Test

```php
<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;


/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Controller
{
    function index()
    {
        $this->response()->write('Hello World');
    }
}
```

### 1 Core 1G RAM

> command : ab -c 100 -n 10000 http://192.168.0.11:9501/

```
Server Software:        EasySwoole
Server Hostname:        192.168.0.11
Server Port:            9501

Document Path:          /
Document Length:        21 bytes

Concurrency Level:      100
Time taken for tests:   0.652 seconds
Complete requests:      10000
Failed requests:        0
Write errors:           0
Total transferred:      1690000 bytes
HTML transferred:       210000 bytes
Requests per second:    15325.16 [#/sec] (mean)
Time per request:       9.685 [ms] (mean)
Time per request:       0.097 [ms] (mean, across all concurrent requests)
Transfer rate:          2592.05 [Kbytes/sec] received
```

### 8 Core 16G RAM

> command : ab -c 100 -n 10000 http://192.168.0.4:9501/

```
Server Software:        EasySwoole
Server Hostname:        192.168.0.4
Server Port:            9501

Document Path:          /
Document Length:        21 bytes

Concurrency Level:      100
Time taken for tests:   0.746 seconds
Complete requests:      10000
Failed requests:        0
Write errors:           0
Total transferred:      1690000 bytes
HTML transferred:       210000 bytes
Requests per second:    66935.97 [#/sec] (mean)
Time per request:       1.149 [ms] (mean)
Time per request:       0.015 [ms] (mean, across all concurrent requests)
Transfer rate:          2265.40 [Kbytes/sec] received
```

## Quick Start
```
composer require easyswoole/easyswoole=3.4.x
php vendor/bin/easyswoole install
php easyswoole.php server start
```

## Unit Test
after install easyswoole,run:
```
php easyswoole.php phpunit ./vendor/easyswoole/easyswoole/tests
```

## Docker
### Get Docker Image
```
docker pull easyswoole/easyswoole3
```
### Run

```
docker run -ti -p 9501:9501 easyswoole/easyswoole3
```
- WorkerDir: ***/easyswoole***
- Run Easyswoole : ***php easyswoole.php server start*** 

## Others 
- [Home Page](https://www.easyswoole.com)
- [Git For Doc](https://github.com/easy-swoole/doc)
- [Git For Demo](https://github.com/easy-swoole/demo)
- QQ exchange group
    - VIP group 579434607 (this group needs to pay 599 RMB)
    - EasySwoole official group 633921431 (full)
    - EasySwoole official two groups 709134628 (full)
    - EasySwoole official three groups 932625047 (full)
    - EasySwoole official four groups 779897753 (full)
    - EasySwoole official five groups 853946743 (full)
    - EasySwoole official six groups 524475224 (full)
    - EasySwoole official seven groups 1016674948
    
- Business support:
    - QQ 291323003
    - EMAIL admin@fosuss.com
