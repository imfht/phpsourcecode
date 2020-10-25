<?php
/**
 * 创建一个空模板
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_Resource_CreateController extends Aj_AbsController {

    //控制器入口
    public function indexAction() {
        $tpl_id = Comm\Arg::post('tpl_id', FILTER_VALIDATE_INT);
        $resource_name = Comm\Arg::post('resource_name');

        $result = Model\Theme\Resource::addResource($tpl_id, $resource_name);

        Comm\Response::json(100000, '保存成功', ['result' => $result], false);
    }

}
