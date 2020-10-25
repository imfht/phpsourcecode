<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 4/4/14
 * Time: 9:29 AM
 */

namespace Api\Controller;

use Think\Controller;
use Weibo\Api\WeiboApi;

class WeiboController extends ApiController
{
    private $weiboApi;

    public function _initialize()
    {
        $this->weiboApi = new WeiboApi();
    }

    public function listAllWeibo($page = 1, $count = 10)
    {
        $result = $this->weiboApi->listAllWeibo($page, $count);
        $this->ajaxReturn($result);
    }

    public function listMyFollowingWeibo($page = 1, $count = 10)
    {
        $result = $this->weiboApi->listMyFollowingWeibo($page, $count);
        $this->ajaxReturn($result);
    }

    public function getWeiboDetail($weibo_id)
    {
        $result = $this->weiboApi->getWeiboDetail($weibo_id);
        $this->ajaxReturn($result);
    }

    public function sendWeibo($content)
    {
        $result = $this->weiboApi->sendWeibo($content);
        $this->ajaxReturn($result);
    }

    public function sendComment($weibo_id, $content, $comment_id = 0)
    {
        $result = $this->weiboApi->sendComment($weibo_id, $content, $comment_id);
        $this->ajaxReturn($result);
    }

    public function listComment($weibo_id, $page = 1, $count = 10)
    {
        $result = $this->weiboApi->listComment($weibo_id, $page, $count);
        $this->ajaxReturn($result);
    }

    public function deleteWeibo($weibo_id)
    {
        $result = $this->weiboApi->deleteWeibo($weibo_id);
        $this->ajaxReturn($result);
    }

    public function deleteComment($comment_id)
    {
        $result = $this->weiboApi->deleteComment($comment_id);
        $this->ajaxReturn($result);
    }
}