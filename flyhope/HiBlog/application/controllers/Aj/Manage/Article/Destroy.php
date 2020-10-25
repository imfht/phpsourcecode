<?php
/**
 * 删除一篇文章
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Article_destroyController extends Aj_AbsController {

    public function indexAction() {
        $id = Comm\Arg::post('id');
        $result = Model\Article::destory($id);
        Comm\Response::json(100000, _('删除成功'), null, false);
    }

}
