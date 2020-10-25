<?php
namespace app\member\controller\wxapp;

use app\member\model\Address AS AddressModel;
use app\common\controller\MemberBase;

//小程序 用户收货地址
class Address extends MemberBase
{    
    /**
     * 新增地址
     * @return \think\response\Json
     */
    public function add(){
        if(!$this->user){
            return $this->err_js('你还没登录');
        }
        $data = input();
        $data['uid'] = $this->user['uid'];
        $result = AddressModel::create($data);
        if($result){
            if($data['often']){
                //不能出现几个默认地址
                AddressModel::where('uid',$this->user['uid'])->where('id','<>',$result->id)->update(['often'=>0]);
            }
            return $this->ok_js(['id'=>$result->id]);
        }else{
            return $this->err_js('新增失败');
        }
    }
    
    /**
     * 删除地址
     * @param unknown $id
     * @return \think\response\Json
     */
    public function delete($id){
        $info = getArray(AddressModel::get($id));
        if($info['uid']!=$this->user['uid']){
            return $this->err_js('你没权限');
        }
        if(AddressModel::destroy($id)){
            return $this->ok_js();
        }else{
            return $this->err_js('删除失败');            
        }
    }
    
    /**
     * 修改地址
     * @param number $id
     * @return \think\response\Json
     */
    public function edit($id=0){
        $info = getArray(AddressModel::get($id));        
        if($info['uid']!=$this->user['uid']){
            return $this->err_js('你没权限');
        }
        $data = input();
        if($data['type']=='post'){            
            $array = [
                    'id'=>$id,
                    'sex'=>$data['sex'],
                    'user'=>$data['user'],
                    'telphone'=>$data['telphone'],
                    'address'=>$data['address'],
                    'often'=>$data['often'],
            ];
            if(AddressModel::update($array)){
                return $this->ok_js([],'修改成功');
            }else{
                return $this->err_js('修改失败');
            }
        }
        return $this->ok_js($info);
    }
    
    
    /**
     * 会员中心列出所有联系地址
     * @return \think\response\Json
     */
    public function index(){
        $listdb = AddressModel::where('uid',$this->user['uid'])->order('id desc')->column(true);        
        return $this->ok_js($listdb);
    }
    
    /**
     * 设置默认地址
     * @param number $id
     * @return \think\response\Json
     */
    public function setDefault($id=0){
        $map = [
                'uid'=>$this->user['uid'],
        ];
        AddressModel::where($map)->update(['often'=>0]);
        $map['id'] = $id;
        if( AddressModel::where($map)->update(['often'=>1]) ){
            return $this->ok_js();
        }else{
            return $this->err_js('设置失败');
        }
    }
    
    /**
     * 显示某个地址
     * @param number $id
     * @return \think\response\Json
     */
    public function show($id=0){
        $map = [
                'uid'=>$this->user['uid'],
                'id'=>$id,
        ];
        $info = getArray(AddressModel::where($map)->find());
        return $this->ok_js($info);
    }
    
    /**
     * 获取默认地址
     * @return \think\response\Json
     */
    public function getDefault(){
        $info = getArray(AddressModel::where('uid',$this->user['uid'])->order('often desc,id desc')->find());
        if($info){
            return $this->ok_js($info);
        }else{
            return $this->err_js('没有可用地址');
        }        
    }
}
