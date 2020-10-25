<?php
/**
 * 删除一个模板资源
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_Resource_DestroyController extends Aj_AbsController {
    
    public function indexAction() {
        $id = Comm\Arg::post('id', FILTER_VALIDATE_INT);
        $result = Model\Theme\Resource::destory($id);
        
        Comm\Response::json(100000, _('操作成功'), ['result' => $result], false);
    }
    
}
