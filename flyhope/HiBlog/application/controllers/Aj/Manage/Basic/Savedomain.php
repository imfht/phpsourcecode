<?php
/**
 * 保存域名信息
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Basic_SavedomainController extends AbsController {
    
    public function indexAction() {
        $domain = Comm\Arg::post('domain');
        Model\Publish::domain($domain);
        Comm\Response::json(100000, '操作成功', null, false);
    }
    
}