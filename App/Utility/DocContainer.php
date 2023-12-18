<?php
/**
 * This file is part of EasySwoole.
 * @link https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact https://www.easyswoole.com/Preface/contact.html
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Utility;

use App\Model\Document\Doc;
use EasySwoole\Component\Singleton;

class DocContainer
{
    protected $container = [];

    use Singleton;

    public function add(Doc $doc)
    {
        $this->container[$doc->getName()] = $doc;
    }

    public function get(string $name): ?Doc
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        } else {
            return null;
        }
    }

    public function all()
    {
        return $this->container;
    }
}
