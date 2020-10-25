<?php
/**
 * 添加评论接口,重要参数moduleuid是正在查看新闻，汇报的作者uid
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\core\utils\StringUtil;
use application\modules\message\model\Comment;

class AddComment extends Base
{

    public function run()
    {
        $data = $this->data;
        $type = $data['type'];
        $rowid = $data['rowid'];

        // $type 参数只支持：comment（评论）和 reply（回复）
        if (!in_array($type, array("comment", "reply"))) {
            $msg = Ibos::lang("Error param") . "请检查 type 参数";
            return $this->getController()->ajaxReturn(array("isSuccess" => false, "msg" => $msg, 'data' => ''));
        }
        //评论类型
        if ("comment" === $type) {
            $list['module'] = Ibos::getCurrentModuleName();
            $list['table'] = Ibos::getCurrentModuleName();
            $language = Ibos::lang('Comment success');
        }elseif ("reply" == $type) {
            $list['module'] = "message";
            $list['table'] = "comment";
            $list['tocid'] = $rowid;
            $language = Ibos::lang('Reply success');
        }
        $list['content'] = StringUtil::parseHtml(\CHtml::encode($data['content']));
        $list['rowid'] = $data['rowid'];
        $list['moduleuid'] = Ibos::app()->user->uid;
        if (isset($data['tocid'])){
            $list['tocid'] = $data['tocid'];
        }
        $urlRoot = $this->controller->createUrl('report/default/index');
        $list['url'] = $urlRoot . "#detail/{$rowid}";
        if (isset($data['detail'])){
            $list['detail'] = $data['detail'];
        }
        $list['touid'] = isset($data['touid']) ? $data['touid'] : 0;
        $cid = Comment::model()->addComment($list);
        $content = Comment::model()->fetchByPk($cid);
        $content['content'] = StringUtil::parseHtml($content['content']);
        $content['content'] = StringUtil::purify($content['content']);
        if ($cid > 0) {
            Ibos::app()->controller->ajaxReturn(array(
                'isSuccess' => true,
                'msg' => $language,
                'data' => $content,
            ));
        }
    }
}