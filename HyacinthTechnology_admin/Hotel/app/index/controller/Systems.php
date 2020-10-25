<?php
namespace app\index\controller;

use app\BaseController;

use app\index\validate\Guest;
use app\index\validate\Identity;
use app\index\validate\Payment;
use think\exception\ValidateException;
use think\facade\Db;

/*
 * 系统设置
 *
 * */

class Systems extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        parent::initialize();
    }

    /*
     * 支付方式
     * */
    public function index()
    {
        $list = $this->select_all('payment');
        return view('index',['list' => $list]);
    }

    /*
     * 添加支付方式
     * */
    public function adds(){
        if(request()->isAjax()){
            return $this->db_add('payment',new Payment());
        }
        return view();
    }

    /*
     * 编辑支付方式
     * */
    public function edits(){
        $list = $this->select_find('payment',['id' => input('id')]);
        if(request()->isAjax()){
            //编辑数据
            return $this->db_edit('payment');
        }
        return view('edits',['list' => $list]);
    }

    /*
     * 删除支付方式
     * */
    public function deletes(){
        if(request()->isAjax()){
            //判断是否删除成功
            if(Db::table('payment')->where('id',input('id'))->delete()){
                return $this->return_json('删除成功','100');
            }else{
                return $this->return_json('删除失败','0');
            }
        }
    }


    /*
     * 宾客来源
     * */
    public function guest(){
        $list = $this->select_all('guest');
        return view('guest',['list' => $list]);
    }

    /*
     * 宾客添加
     * */
    public function guest_adds(){
        if(request()->isAjax()){
            return $this->db_add('guest',new Guest());
        }
        return view();
    }

    /*
     * 宾客编辑
     * */
    public function guest_edits(){
        $list = $this->select_find('guest',['id' => input('id')]);
        if(request()->isAjax()){
            //编辑数据
            return $this->db_edit('guest');
        }
        return view('guest_edits',['list' => $list]);
    }
    /*
     * 删除宾客来源
     * */
    public function guest_deletes(){
        if(request()->isAjax()){
            //判断是否删除成功
            if(Db::table('guest')->where('id',input('id'))->delete()){
                return $this->return_json('删除成功','100');
            }else{
                return $this->return_json('删除失败','0');
            }
        }
    }


    /*
     * 证件设置
     * */
    public function identity(){
        $list = $this->select_all('identity');
        return view('identity',['list' => $list]);
    }
    /*
     * 证件添加
     * */
    public function identity_adds(){
        if(request()->isAjax()){
            return $this->db_add('identity',new Identity());
        }
        return view();
    }

    /*
     * 证件编辑
     * */
    public function identity_edits(){
        $list = $this->select_find('identity',['id' => input('id')]);
        if(request()->isAjax()){
            //编辑数据
            return $this->db_edit('identity');
        }
        return view('identity_edits',['list' => $list]);
    }
    /*
     * 删除宾客来源
     * */
    public function identity_deletes(){
        if(request()->isAjax()){
            //判断是否删除成功
            if(Db::table('identity')->where('id',input('id'))->delete()){
                return $this->return_json('删除成功','100');
            }else{
                return $this->return_json('删除失败','0');
            }
        }
    }
}
