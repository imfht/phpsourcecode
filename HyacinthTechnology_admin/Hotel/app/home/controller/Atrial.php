<?php
namespace app\home\controller;

use app\index\controller\Basics;
use think\facade\Db;
/*
 * 房态管理
 * */
class Atrial extends Basics
{

    /*
     * 置空房间
     * */
    public function emptys(){
        if(request()->isAjax()){
            if( Db::name('room')->update(input('param.'))){
                return $this->return_json('操作成功','100');
            }else{
                return $this->return_json('操作失败','0');
            }
        }
    }

    /*
     * 故障报修
     * */
    public function report(){
        if(request()->isAjax()){
            $data = input('param.');
            $data['status'] = '4';
            if( Db::name('room')->update($data)){
                return $this->return_json('操作成功','100');
            }else{
                return $this->return_json('操作失败','0');
            }
        }
    }

    /*
     * 退房业务
     * */
    public function refund()
    {
;       if(request()->isAjax()){
            $data = [
                'status' => 1,
                'room_id' => 'no',
                'guest_name' => '',
                'activity_id' => '',
                'credentials' => '',
                'guest_sex' =>'',
                'guest_source' =>'',
                'guest_number' =>'',
                'move_duration' =>'',
                'move_time' => ''
            ];
            $res  = $this->select_find('room',['id' => input('id')]);
            if($res['room_id'] != 'no'){
                Db::name('room')->where('id',input('id'))->update($data);
//                return $this->return_json('不是主客房','0');
                $id= input('id');
                $map = "room_id={$id} OR superior_id={$id}";
                $res = Db::name('income')->where($map)->update(['status' => '0']);
                return $this->return_json('不是主客房(退房成功)','100');
            }



            if( Db::name('room')->where('id',input('id'))->update($data)){
                Db::name('room')->where('room_id',input('id'))->update($data);
                //更新收入明细
                $id= input('id');
                $map = "room_id={$id} OR superior_id={$id}";
                $res = Db::name('income')->where($map)->update(['status' => '0']);
                return $this->return_json('操作成功','100');
            }else{
                return $this->return_json('操作失败','0');
            }
        }
    }

    /*
     * 添加消费
     * */
    public function consume(){
        dump(input('id'));

        $list = $this->select_find('room',['id' => input('id')]);
//        $data = Db::name('consume')->where('room_id',input('id'))->paginate(10);
        $data =  Db::table('consume')
            ->alias('a')
            ->field('a.*,b.room_num,d.number,d.name,d.price')
            ->join('room b','a.room_id = b.id')
            ->join('goodss d','a.goods_id = d.id')
            ->where('a.room_id',input('id'))
            ->paginate(10);
        dump($data);
        return view('consume',['list' => $list,'data' => $data]);
    }

    /*
     * 选择添加商品
     * */
    public function goods(){

        if(request()->isAjax()){
            $data = input('param.');
            $data['create_time'] = time();
            $list = $this->select_find('goodss',['id'=>$data['goods_id']]);
            $res = $list['number'] - $data['num'];
            if( Db::name('consume')->insert($data)){
                //去减库存
                Db::table('goodss')->where('id',$data['goods_id'])->update(['number' => $res]);
                return $this->return_json('操作成功','100');
            }else{
                return $this->return_json('操作失败','0');
            }
        }
/*        $list =  Db::table('purchases')
            ->alias('a')
            ->field('a.*,b.name,b.price')
            ->join('goodss b','a.goods_id = b.id')
            ->paginate(10);*/
        $list =  Db::table('goodss')->where('building_id',session('building_id'))->paginate(10);
        return view('goods',['list' => $list,'room_id'=> input('room_id')]);
    }

    /*
     * 选择删除商品
     * */
    public function delgoods(){

        if(request()->isAjax()){

            $res = Db::table('consume')->where('id',input('id'))->find();
            $list = Db::table('goodss')->where('id',$res['goods_id'])->find();
            $data = [
                'id' => $res['goods_id'],
                'number' => $list['number'] + $res['num']
            ];
            
            if(  Db::table('consume')->delete(input('id'))){
                Db::table('goodss')->update($data);
                return $this->return_json('操作成功','100');
            }else{
                return $this->return_json('操作失败','0');
            }
        }
    }
}
