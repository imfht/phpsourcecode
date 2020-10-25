<?php
namespace Home\Controller;

/**
 *
 * 网站信息
 */
class SiteinfoController extends CommonController {

    public function index() {
        $SiteinfoService = D("Siteinfo","Service");
        $result = $SiteinfoService->getSiteinfo();
        $this->assign('siteinfo',$result[0]);
        $this->display();
    }

    public function saveOrUpdate(){
        $siteinfo = $_POST['siteinfo'];
        $result =  D("Siteinfo","Service")->saveOrUpdate($siteinfo);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        session('__siteinfo__',$siteinfo);
        $this->successReturn('网站信息操作成功！', U('Siteinfo/index'));
    }


}
