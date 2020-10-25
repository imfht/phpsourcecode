<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 14-3-19
 * Time: 下午2:19
 */
namespace Addons\InsertImage\Controller;

use Home\Controller\AddonsController;

class InsertImageController extends AddonsController
{


    public function imageBox()
    {
// 返回的JSON值
        $data['unid'] = substr(strtoupper(md5(uniqid(mt_rand(), true))), 0, 8);
        $data['status'] = 1;
        $data['total'] = 9;
        // 设置渲染变量
        $var['unid'] = $data['unid'];

        $var['fileSizeLimit'] = floor(2 * 1024).'KB';
        $var['total'] = $data['total'];
        $this->assign($var);
        $data['html'] = $this->fetch('imageBox');
        exit(json_encode($data));
        dump( $data['html']);

    }
}

