<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;


/*
 * 价格体系
 *
 * */

class Pricesystem extends Basics
{

    // 初始化
    protected function initialize()
    {
        parent::initialize();
    }

    /*
     * 编辑房型价格
     * */
    public function edits(){
        $data = input('param.');
        $res = Db::name('week')->where('id', $data['id'])->update($data);
        if($res){
            return $this->return_json('编辑成功','100');
        }else{
            return $this->return_json('编辑失败','0');
        }
    }

/**********************************8*****8********8***/

    public function index(){
        if(request()->isPost()){
            if(input('type') == '1'){
                $week = $this->count_weekss('1');
            }else{
                $week = $this->count_weekss();
            }
        }else{
            $week = $this->count_weekss();
        }


        $list = Db::table('week')
            ->alias('a')
            ->field('a.*,b.room_num,b.type_id,c.type_name,c.price')
            ->join(['room'=>'b'],'a.layout_id=b.id')
            ->join(['layout'=>'c'],'b.type_id=c.id')
            ->paginate(10);
        return view('index',['list' => $list,'week' => $week,'types' => input('type')]);
    }

    /*
     * 计算一周后星期几
     * */
    public function count_weekss($type = null,$max = 7){
        date("l"); //data就可以获取英文的星期比如Sunday
        date("w"); //这个可以获取数字星期比如123，注意0是星期日

        $weekarray=array("日","一","二","三","四","五","六"); //先定义一个数组
        $arr = [];
        for($x=0; $x<$max; $x++){
            if($type == null){
                array_push($arr, date("m/d",strtotime("{$x} day"))."/星期".$weekarray[date("w",strtotime("{$x} day"))]);
            }else{
                $num= 7+ $x;
                array_push($arr, date("m/d",strtotime("{$num} day"))."/星期".$weekarray[date("w",strtotime("{$num} day"))]);
            }
        }
        return $arr;
    }

}
