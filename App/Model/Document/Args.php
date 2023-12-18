<?php
/**
 * This file is part of EasySwoole.
 * @link https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact https://www.easyswoole.com/Preface/contact.html
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Model\Document;

class Args
{
    protected $args = [];

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    public function setArg($key, $val)
    {
        $this->args[$key] = $val;
    }

    public function getArg($key)
    {
        if (isset($this->args[$key])) {
            return $this->args[$key];
        }
        return null;
    }
}
