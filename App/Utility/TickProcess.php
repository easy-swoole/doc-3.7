<?php
/**
 * This file is part of EasySwoole.
 * @link https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact https://www.easyswoole.com/Preface/contact.html
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Utility;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Config;
use Swoole\Coroutine\System;

class TickProcess extends AbstractProcess
{
    protected function run($arg)
    {
        Timer::getInstance()->loop(30 * 1000, function () {
            // 写入搜索内容json
            $list = Config::getInstance()->getConf("DOC.ALLOW_LANGUAGE");
            try {
                foreach ($list as $dir => $value) {
                    $json = DocSearchParser::parserDoc2JsonUrlMap(EASYSWOOLE_ROOT, "{$dir}");
                    file_put_contents(EASYSWOOLE_ROOT . "/Static/keyword{$dir}.json", json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            } catch (\Throwable $throwable) {
                \EasySwoole\EasySwoole\Trigger::getInstance()->throwable($throwable);
            }

            // 本项目是git克隆下来的，因此自动同步
            $exec = "cd " . EASYSWOOLE_ROOT . "; git pull";
            System::exec($exec);
        });
    }
}
