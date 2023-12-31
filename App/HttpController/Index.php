<?php
/**
 * This file is part of EasySwoole.
 * @link https://www.easyswoole.com
 * @document https://www.easyswoole.com
 * @contact https://www.easyswoole.com/Preface/contact.html
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use App\Model\Document\Doc;
use App\Utility\DocContainer;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->actionNotFound('index');
    }

    protected function actionNotFound(?string $action)
    {
        $host = $this->request()->getUri()->getHost();
        switch ($host) {
            case 'swoole.easyswoole.com':
            {
                $doc = 'SWOOLE_DOC';
                break;
            }
            case 'english.easyswoole.com':
            {
                $doc = 'ES_DOC_EN';
                break;
            }
            default:
            {
                $doc = 'ES_DOC_CN';
                break;
            }
        }
        /** 调试的时候，强制指定 $doc */
//        $doc = 'ES_DOC_CN';
        $doc = DocContainer::getInstance()->get($doc);
        if ($doc instanceof Doc) {
            $doc->display($this->request(), $this->response());
        } else {
            $this->response()->write('not language match');
        }
    }
}
