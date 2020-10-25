<?php
/**
 * 删除主题
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_DestroyController extends Aj_AbsController {

    public function indexAction() {
        $theme_id = Comm\Arg::post('id');
        $result = Model\Theme\Main::destory($theme_id);
        Comm\Response::json(100000, '操作成功', ['result' => $result], false);
    }


}
