<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 11:03
 */

namespace app\weixin\controller;


use think\Controller;

class Order extends Controller
{

    public function index(){
        $uid = input('uid');
        $cart = input('cart');
        $postprice = input('postprice');
        $arr = json_decode($cart,true);
        $ordercord =time().rand_string(10,1);
        $tt = $arr['productlist'];
        foreach((array)$tt as $k=>$v){
            $datalist['good_id'] = $v['id'];
            $datalist['good_price'] = $v['mprice'];
            $datalist['good_num'] = $v['num'];
            $datalist['ordercode']=$ordercord;
            $datalist['good_name']=$v['name'];
            db('order_goods')->insert($datalist);
        }
        $data['ordercode']=$ordercord;
        $data['nums']=$arr['totalNumber'];
        $data['price']=$arr['totalAmount'];
        //return json($arr['totalAmount']);
        $data['stauts']=0;
        $data['uid']=$uid;
        $data['creattime']=time();
        $data['postprice']=$postprice;
        $data['sprice']=($postprice+$arr['totalAmount']);
        if(db('order')->insert($data)){
            return json(['ordercode'=>$ordercord,'code'=>1]);
        }else{
            return json(['code'=>2]);
        }
    }


    public function orderlist(){
        $code = input('ordercode');
        $uid = input('userId');
        $addressId = input('addressId');
        $list = db('order')->where('ordercode',$code)->find();
        $res = db('order_goods')->where('ordercode',$code)->select();
        foreach ($res as $k=>$v){
            $one = db('good')->field('good_img')->where('good_id',$v['good_id'])->find();
            $res[$k]['good_img']= request()->root(true).'/uploads/'.$one['good_img'];
        }
        $list['productlist']=$res;

        return json($list);
    }


    public function userorder(){
        $uid = input('uid');
        //获取未付款的订单数
        $unpaid = db('order')->where('stauts = 0 and uid= '.$uid)->count();
        $send = db('order')->where('stauts = 1 and uid= '.$uid)->count();
        $sendtime = db('order')->where('stauts = 2 and uid= '.$uid)->count();
        $outpay = db('order')->where('stauts = 3 and uid= '.$uid)->count();
        $orderbj = db('order')->where('stauts = 4 and uid= '.$uid)->count();

        return json(['nopay'=>$unpaid,'send'=>$send,'sendtime'=>$sendtime,'outpay'=>$outpay,'orderbj'=>$orderbj]);

    }

    public function userallorder(){
        $uid = input('uid');
        $res=db('order')->where('uid',$uid)->order('orderId desc')->select();
        foreach ($res as $k=>$v){
            $goodres = db('order_goods')->where('ordercode',$v['ordercode'])->select();
            foreach ($goodres as $k1=>$v1){
                $one = db('good')->field('good_img')->where('good_id',$v1['good_id'])->find();
                $goodres[$k1]['good_img']= request()->root(true).'/uploads/'.$one['good_img'];
            }
            $res[$k]['goodlist']=$goodres;
        }
        return json($res);
    }


    public function userorderlist(){
        $uid = input('uid');
        $status = input('status');
        $res=db('order')->where('uid = '.$uid.' and stauts='.$status)->order('orderId desc')->select();
        foreach ($res as $k=>$v){
            $goodres = db('order_goods')->where('ordercode',$v['ordercode'])->select();
            foreach ($goodres as $k1=>$v1){
                $one = db('good')->field('good_img')->where('good_id',$v1['good_id'])->find();
                $goodres[$k1]['good_img']= request()->root(true).'/uploads/'.$one['good_img'];
            }
            $res[$k]['goodlist']=$goodres;
        }
        return json($res);
    }
}