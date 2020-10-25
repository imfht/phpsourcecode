<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\Document as LogicDocument;
use \app\common\controller\ControllerBase;

/**
 * 首页控制器
 */
class Index extends ControllerBase
{

    /**
     * 首页方法
     */
    public function index()
    {

        $documentLogic = get_sington_object('documentLogic', LogicDocument::class);

        $list = $documentLogic->getApiList();

        $code_list = $documentLogic->apiErrorCodeData();

        $this->assign('code_list', $code_list);

        $content = $this->fetch('content_default');

        $this->assign('content', $content);

        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     * API详情
     */
    public function details($id = 0)
    {

        $documentLogic = get_sington_object('documentLogic', LogicDocument::class);

        $list = $documentLogic->getApiList();

        $info = $documentLogic->getApiInfo(['id' => $id]);

        $this->assign('info', $info);


        $this->assign('test_access_token', get_access_token());

        $content = $this->fetch('content_template');

        if (IS_AJAX) : exit($content); endif;

        $this->assign('content', $content);

        $this->assign('list', $list);

        return $this->fetch('index');
    }
}
