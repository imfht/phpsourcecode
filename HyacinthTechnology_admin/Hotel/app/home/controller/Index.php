<?php
namespace app\home\controller;

use app\home\validate\Into;
use app\index\controller\Basics;
use think\facade\Db;

class Index extends Basics
{
    /*
     * 前台首页
     * */
    public function index()
    {
        $list =  Db::table('admin')
                    ->alias('a')
                    ->field('a.*,b.building')
                    ->join('building b','a.building_id = b.id')
                    ->where('a.username',session('admin'))
                    ->find();
        session('building_id',$list['building_id']);
        return view('index',['list' => $list]);
    }
    /*
     * 房间动态
     * */
    public function welcome(){
        $list =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit,c.building,d.storey')
            ->join('layout b','a.type_id = b.id')
            ->join('building c','a.building_id = c.id')
            ->join('storey d','a.storey_id = d.id')
            ->where('a.building_id',session('building_id'))
            ->select();
//        dump($list);
        if(request()->isAjax()){
            return json($list);
        }
        return view('welcome',['list' => $list]);
    }


    /*
     * 办理入住
     * */
    public function handle(){
        //办理入住
        if(request()->isAjax()){
            $validate = new Into();
            if (!$validate->check(input('param.'))) {
                return $this->return_json($validate->getError(),'0');
            }

            if($this->select_find('member',['id'=> input('member_id')])){
                $data = input('param.');
                $data['status'] = '3';
                $data['create_time'] = time();
            }else{
                $data = input('param.');
                $data['create_time'] = time();
            }
            //保存数据记录
            $this->record($data);

            if( Db::name('room')->update($data)){
//                Db::name('income')->insert($datas);
                return $this->return_json('入住成功','100');
            }else{
                return $this->return_json('入住失败','0');
            }
        }
        $id  = input('id');
        $activity = $this->select_all('activitys');//促销活动
        $identity = $this->select_all('identity');//证件类型
        $guest = $this->select_all('guest');//宾客来源
        $payment = $this->select_all('payment');//支付方式
        $member = Db::name('member')->where(['building_id'=>session('building_id')])->paginate('10');
//        dump($member);
        $list = $this->select_find('room',['id'=>$id]);

        //查询追加的房间
        $map = [
            ['a.building_id','=',session('building_id')],
            ['a.room_id','=',$id],
            ['a.id','<>',$id]
        ];
        $show =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit,c.building,d.storey')
            ->join('layout b','a.type_id = b.id')
            ->join('building c','a.building_id = c.id')
            ->join('storey d','a.storey_id = d.id')
            ->where($map)
            ->select();
//        dump( Db::table('room')->getLastSql());
        return view('handle',['activity' => $activity,'identity' => $identity,'guest' => $guest,'payment' => $payment,'list' => $list,'member' => $member,'show' => $show]);
    }


    /*
     * 入住房间价格和记录收入信息
     * */
    public function record($data){

        if(isset($data['room_id'])){
           $datas['superior_id'] = $data['room_id'];
        }
        //先查询今日的价格
        $list = $this->select_find('week',['layout_id' => $data['id']]);
        $datas['income_details'] = $list['monday'];
        $datas['room_id'] = $data['id'];
        //查询押金
        $show =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit')
            ->join('layout b','a.type_id = b.id')
            ->where(['a.id' => $data['id']])
            ->find();
        $datas['deposit_record'] = $show['deposit'] ;
        $datas['create_time'] = time();
        //查询优惠活动
        if(isset($data['activity_id'])){
            $list = $this->select_find('activitys',['id' => $data['activity_id']]);
            if(strtotime($list['start']) <= time() && strtotime($list['end']) <= time() ){
                $datas['activity_price'] = $list['price'];
            }else{
                $datas['activity_price'] = '1';
            }
        }

        //会员折扣
        if(isset($data['member_id']) && $data['member_id'] != 0){
            $member =  Db::table('member')
                ->alias('a')
                ->field('a.*,b.price')
                ->join('viptype b','a.type = b.id')
                ->where(['a.id' => $data['member_id']])
                ->find();
            $datas['member_price'] = $member['price'];
        }else{
            $datas['member_price'] = '1';
        }
        Db::name('income')->save($datas);

    }

    /*
     * 追加房间
     * */
    public function addroom(){
        if(request()->isAjax()){
            $res =  Db::name('room')
                    ->where('id', input('id'))
                    ->update(['room_id' => input('room_id'),'status' => 2,'create_time' => time()]);
            $this->record(input('param.'));
            if($res){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }

        $map = [
            ['a.building_id','=',session('building_id')],
            ['a.room_id','=','no'],
            ['a.id','<>',input('room_id')]
        ];
        $list =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit,c.building,d.storey')
            ->join('layout b','a.type_id = b.id')
            ->join('building c','a.building_id = c.id')
            ->join('storey d','a.storey_id = d.id')
            ->where($map)
            ->select();
//        dump( Db::table('room')->getLastSql());
//        dump($list);
        return view('addroom',['list' => $list,'room_id' =>  input('room_id')]);
    }

    /*
     * 移除房间
     * */
    public function remove(){
        if(request()->isAjax()){
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
            $res =  Db::name('room')
                ->where('id', input('id'))
                ->update($data);
            if($res){
                Db::name('income')->where('room_id', input('id'))->delete();
                return $this->return_json('移除成功','100');
            }else{
                return $this->return_json('移除失败','0');
            }
        }
    }

    /*
     * 随客管理
     * */
    public function peers(){
        $map = [
            ['a.building_id','=',session('building_id')],
            ['a.room_id','=',input('room_id')],
            ['a.id','<>',input('room_id')]
        ];
        $list =  Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price,b.deposit,c.building,d.storey')
            ->join('layout b','a.type_id = b.id')
            ->join('building c','a.building_id = c.id')
            ->join('storey d','a.storey_id = d.id')
            ->where($map)
            ->select();
        $identity = $this->select_all('identity');//证件类型
        return view('peers',['list' => $list,'identity' => $identity]);
    }

    /*
     * 随客资料添加
     * */
    public function addpeers(){
        if(request()->isAjax()){
            if( Db::name('room')->update(input('param.'))){
                return $this->return_json('入住成功','100');
            }else{
                return $this->return_json('入住失败','0');
            }
        }
        return view('addpeers',['id' => input('id')]);
    }

    /*
     * 会员选择
     * */
    public function select_member(){
        $data = $this->select_find('member',['id' => input('id')]);
        return json($data);
    }
}
