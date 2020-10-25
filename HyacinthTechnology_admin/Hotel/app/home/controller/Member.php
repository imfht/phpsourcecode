<?php
namespace app\home\controller;

use app\index\controller\Basics;
use think\facade\Db;

class Member extends Basics
{
    /*
     * 会员 首页
     * */
    public function index()
    {
        $list =  Db::table('member')
            ->alias('a')
            ->field('a.*,b.vipname')
            ->join('viptype b','a.type = b.id')
            ->where('a.building_id',session('building_id'))
            ->paginate(10);

        return view('index',['list' => $list]);
    }

    /*
     * 会员 添加
     * */
    public function vip()
    {
        if(request()->isAjax()){
            $data = input('param.');
            $data['create_time'] = time();
            $data['building_id'] = session('building_id');
            //判断是否添加成功
            if(Db::name('member')->insert($data)){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }
//        $list = $this->select_all('viptype');
        $list = Db::table('viptype')->where('building_id',session('building_id'))->select();
        return view('vip',['list' => $list]);
    }

    /*
     * 会员 删除
     * */
    public function delete()
    {
        if(request()->isAjax()){
            $data = input('id');
            if( Db::table('member')->delete($data)){
                return $this->return_json('删除成功','100');
            }else{
                return $this->return_json('删除失败','0');
            }
        }
    }

    /*
     * 会员充值
     * */
    public function recharge(){

        if(request()->isAjax()){
            $data = input('param.');
            //查询原来的余额
            $res = Db::table('member')->where('id',$data['id'])->find();
            //查询充值优惠
/*            $list = Db::table('recharge')->where('building_id',session('building_id'))->select();
            foreach ($list as $value){
                if($data['money'] >= $value['recharge']){
                    $money =$res['money'] + $data['money'] + $value['give'];
                    echo '进来了|'.$value['recharge'];
                    dump($money);
                }
            }*/

            $data['money'] =$res['money'] + $data['money'];
            if( Db::table('member')->update($data)){
                return $this->return_json('充值成功','100');
            }else{
                return $this->return_json('充值失败','0');
            }
        }
        return view('recharge',['id'=>input('id')]);
    }

    /*----------------------------------------------------------------------------------------------------------------------------------*/
    /*
     * 会员 类型
     * */
    public function viptype()
    {
        $list = Db::name('viptype')->where('building_id',session('building_id'))->paginate(10);
        return view('viptype',['list' => $list]);
    }

    /*
     * 会员类型 添加
     * */
    public function vipadd()
    {
        if(request()->isAjax()){
            $data = input('param.');
            $data['create_time'] = time();
            $data['building_id'] = session('building_id');
            //判断是否添加成功
            if(Db::name('viptype')->insert($data)){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }
        return view();
    }

    /*
     * 会员类型 删除
     * */
    public function vipdelete()
    {
        if(request()->isAjax()){
            $data = input('id');
            if( Db::table('viptype')->delete($data)){
                return $this->return_json('删除成功','100');
            }else{
                return $this->return_json('删除失败','0');
            }
        }
    }

}
