<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
class System extends Common
{
    /********************************站点管理*******************************/
    //站点设置
    public function system($sys_id=1){
        $table = db('system');
        if(Request::isAjax()) {
            $data = Request::except('file');
            if($table->where('id',1)->update($data)!==false) {
                savecache('System');
                return json(['code' => 1, 'msg' => '站点设置保存成功!', 'url' => url('system/system')]);
            } else {
                return json(array('code' => 0, 'msg' =>'站点设置保存失败！'));
            }
        }else{
            $system = $table->find($sys_id);
            $this->assign('system', json_encode($system,true));
            return $this->fetch();
        }
    }
    public function email(){
        if(Request::isAjax()) {
            $datas = input('post.');
            foreach ($datas as $k=>$v){
               Db::name('config')->where([['name','=',$k],['inc_type','=','smtp']])->update(['value'=>$v]);
            }
            return json(['code' => 1, 'msg' => '邮箱设置成功!', 'url' => url('system/email')]);
        }else{
            $smtp = Db::name('config')->where('inc_type','smtp')->select();
            $info = convert_arr_kv($smtp,'name','value');
            $this->assign('info', json_encode($info,true));
            return $this->fetch();
        }
    }
    public function trySend(){
        $sender = input('email');
        //检查是否邮箱格式
        if (!is_email($sender)) {
            return json(['code' => 0, 'msg' => '测试邮箱码格式有误']);
        }
        $arr = db('config')->where('inc_type','smtp')->select();
        $config = convert_arr_kv($arr,'name','value');
        $content = $config['test_eamil_info'];
        $send = send_email($sender, '测试邮件',$content);
        if ($send) {
            return json(['code' => 1, 'msg' => '邮件发送成功！']);
        } else {
            return json(['code' => 0, 'msg' => '邮件发送失败！']);
        }
    }

}
