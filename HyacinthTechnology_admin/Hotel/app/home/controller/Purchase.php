<?php
namespace app\home\controller;

use app\index\controller\Basics;
use app\index\validate\Purchases;
use think\facade\Db;

/*
 * 采购订单
 *
 * */

class Purchase extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Purchases';
        $this->new_model();
        $this->validate = new Purchases();
        parent::initialize();
    }

    /*
     * 订单首页
     * */
    public function warehousing()
    {
        $list = Db::name('order')->where('building_id',session('building_id'))->paginate(10);
        return view('warehousing',['list' => $list]);
    }

    /*
     * 订单入库
     * */
    public function index()
    {
        $list = Db::name('order')->where('building_id',session('building_id'))->paginate(10);
        return view('index',['list' => $list]);
    }

    /*
     * 创建订单
     * */
    public function add_order(){
        if(request()->isAjax()){
            $data =[
                'order_num'=> date("Ymd",time()).'-'.rand(1000,9999),
                'create_time' => time(),
                'building_id' => session('building_id')
            ];
            if(Db::name('order')->insert($data)){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }
    }


    /*
     * 订单详情
     * */
    public function details()
    {
//        $list = Db::name('purchases')->where('order_id',input('id'))->paginate(10);

        $map = [
            'a.order_id' => input('id'),
            'a.building_id' => session('building_id')
        ];
        $list =  Db::table('purchases')
            ->alias('a')
            ->field('a.*,b.name,b.price')
            ->join('goodss b','a.goods_id = b.id')
            ->where($map)
            ->paginate(10);
//        dump($list);
        return view('details',['id' => input('id'),'list' => $list]);
    }

    /*
     * 采购订单添加商品
     * */
    public function adds(){

        if(request()->isAjax()){
            //验证字段
            if(!$this->checkDate(input('param.'))){
                return $this->return_json($this->validate->getError(),'0');
            }
            //添加数据
            $data = Db::name('goodss')->where('id',input('id'))->find();
            if(empty($data['number'])){
                $where = input('param.');
            }else{
                $where = [
                    'number' => $data['number'] + input('number'),
                    'order_id' => input('order_id'),
                    'id' => input('id'),
                ];
            }
            Db::name('purchases')->insert([
                'number' => input('number'),
                'order_id' => input('order_id'),
                'goods_id' => input('id'),
                'create_time' => time(),
                'building_id' => session('building_id')
            ]);
            if( Db::name('goodss')->update($where)){
                return $this->return_json('操作成功','100');
            }else{
                return $this->return_json('操作失败','0');
            }
        }
        $list = Db::name('goodss')->where('building_id',session('building_id'))->paginate(10);
        return view('adds',['id' => input('id'),'list' => $list]);
    }

}
