<?php
/**
 * This file is part of EasySwoole.
 * @link https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact https://www.easyswoole.com/Preface/contact.html
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Model\Document;

class Template
{
    protected $homePageTpl;
    protected $sideBarMd;
    protected $contentPageTpl;
    protected $pageNotFoundTpl;

    /**
     * @return mixed
     */
    public function getHomePageTpl()
    {
        return $this->homePageTpl;
    }

    /**
     * @param mixed $homePageTpl
     */
    public function setHomePageTpl($homePageTpl): void
    {
        $this->homePageTpl = $homePageTpl;
    }

    /**
     * @return mixed
     */
    public function getSideBarMd()
    {
        return $this->sideBarMd;
    }

    /**
     * @param mixed $sideBarMd
     */
    public function setSideBarMd($sideBarMd): void
    {
        $this->sideBarMd = $sideBarMd;
    }

    /**
     * @return mixed
     */
    public function getContentPageTpl()
    {
        return $this->contentPageTpl;
    }

    /**
     * @param mixed $contentPageTpl
     */
    public function setContentPageTpl($contentPageTpl): void
    {
        $this->contentPageTpl = $contentPageTpl;
    }

    /**
     * @return mixed
     */
    public function getPageNotFoundTpl()
    {
        return $this->pageNotFoundTpl;
    }

    /**
     * @param mixed $pageNotFoundTpl
     */
    public function setPageNotFoundTpl($pageNotFoundTpl): void
    {
        $this->pageNotFoundTpl = $pageNotFoundTpl;
    }
}
