<?php
namespace app\member\model;
use app\system\model\SystemModel;
/**
 * 第三方登录
 */
class MemberConnectModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'connect_id',
    ];



    public function getLoginList($mobile = false) {
        $platform = $mobile ? 'mobile' : 'web';
        $list = hook('service', 'login', 'member');
        $data = [];
        foreach ((array)$list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }

        $login = [];
        foreach($data as $key => $vo) {
            if($vo['platform'] <> $platform) {
                continue;
            }
            $vo['type'] = $key;
            if($vo['check']) {
                if(target($key . '/Member', 'service')->checkClient($key)) {
                    $login[] = $vo;
                }
            }
        }
        return $login;
    }

}
