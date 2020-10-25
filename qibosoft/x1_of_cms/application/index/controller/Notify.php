<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;

//接口回调
class Notify extends IndexBase
{
    public function index($type='')
    {
        $data = $this->request->post();
        if (empty($data['type'])&&$type) {
            $this->request->post(['type'=>$type]);
			$data['type'] = $type;
        }
        
        //钩子扩展
        $this->get_hook('user_leave',$data);
        hook_listen('user_leave',$data);
        
        if($type=='leave_qun' && $data['valid_time'] && $data['aid']){	//暂时还没做权限检验
            $array = [
                'uid'=>intval($data['uid']),
                'aid'=>$data['aid'],
                'create_time'=>time(),
                'view_time'=>abs($data['valid_time']),
                
            ];
            //file_put_contents(ROOT_PATH.'WS22.TXT', var_export( $array ,true),FILE_APPEND);
            Db::name('alilive_viewlog')->insert($array);
            $info=Db::name('alilive_order')->where('aid',$data['aid'])->field("SIGN(timelong-usetime) AS num,id")->order('num desc,id asc')->find();
            if ($info && $info['num']==1) { //从最早买的先统计.
                Db::name('alilive_order')->where('id',$info['id'])->setInc('usetime',abs($data['valid_time']));
            }
            
        }
    }
}

