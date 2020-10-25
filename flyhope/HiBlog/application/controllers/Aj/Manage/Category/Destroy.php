<?php
/**
 * 删除分类
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Category_DestroyController extends Aj_AbsController {

    public function indexAction() {
        $ids = isset($_POST['ids']) ? $_POST['ids'] : array();

        Model\Category::destroyByUserBatch($ids);

        Comm\Response::json(100000, '操作成功', null, false);
    }

}