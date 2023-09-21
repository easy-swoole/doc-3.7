<?php


namespace EasySwoole\EasySwoole;


use App\Model\Document\Doc;
use App\Utility\DocContainer;
use App\Utility\TickProcess;
use EasySwoole\Component\Process\Manager;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $cn = new Doc(EASYSWOOLE_ROOT . '/Cn');
        $cn->setName('ES_DOC_CN');
        $cn->getTemplate()->setHomePageTpl('index.tpl');
        $cn->getTemplate()->setSideBarMd('sideBar.md');
        $cn->getTemplate()->setContentPageTpl('contentPage.tpl');
        $cn->getTemplate()->setPageNotFoundTpl('404.tpl');
        DocContainer::getInstance()->add($cn);

        $en = new Doc(EASYSWOOLE_ROOT . '/En');
        $en->setName('ES_DOC_EN');
        $en->getTemplate()->setHomePageTpl('index.tpl');
        $en->getTemplate()->setSideBarMd('sideBar.md');
        $en->getTemplate()->setContentPageTpl('contentPage.tpl');
        $en->getTemplate()->setPageNotFoundTpl('404.tpl');
        DocContainer::getInstance()->add($en);

//        Manager::getInstance()->addProcess(new TickProcess());
    }
}
