<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;


class Basics extends BaseController
{
    public $model_name = null;
    public $model;
    protected $validate;

    // 初始化
    protected function initialize()
    {
        if(empty(session('admin'))){
            header("Location:/index/login/index");
        }
        $this->is_dir('Voice.php');
        $this->time_price();//没有则更新日历价格
        $this->upd_price();//根据过期天数更新日历价格
    }

    /*
     * 查询一条数据
     * */
    public function select_find($table,$map){
        return Db::name($table)->where($map)->find();
    }

    /*
     * 控制器查询所以数据
     * */
    public function select_all($table){
        return Db::name($table)->order('id', 'asc')->select();
    }

    /*
     * 显示页面
     *
     * */
    public function index()
    {
        $list = $this->model->select_plus('page');

        return view('index',['list' => $list]);
    }


    /*
     * 模型添加数据
     * */
    public function adds(){
        if(request()->isAjax()){
            //验证字段
            if(!$this->checkDate(input('param.'))){
                return $this->return_json($this->validate->getError(),'0');
            }
            //添加数据
            return $this->model->add_plus();
        }
        return view();
    }

    /*
     * 模型编辑数据
     * */
    public function edits(){

        $list = $this->select_find(strtolower($this->model_name),['id' => input('id')]);
        if(request()->isAjax()){
            //验证字段
/*            if(!$this->checkDate(input('param.'))){
                return $this->return_json($this->validate->getError(),'0');
            }*/
            //编辑数据
            return $this->model->edit_plus();
        }
        return view('edits',['list' => $list]);
    }

    /*
     * 删除操作
     * */
    public function deletes(){
        return $this->model->delete_plus();
    }


    /*
     * 控制器添加数据
     * */
    public function  db_add($table,$checks =null){

        $data = input('param.');
        $data['create_time'] = time();
        if($checks = null){
            //验证数据
            if(!$checks->check($data)){
                return $this->return_json($checks->getError(),'0');
            }
        }
        //判断是否添加成功
        if(Db::name($table)->insert($data)){
            return $this->return_json('新增成功','100');
        }else{
            return $this->return_json('新增失败','0');
        }
    }

    /*
     * 控制器编辑数据
     * */
    public function db_edit($table){
        if(Db::name($table)->update(input('param.'))){
            return $this->return_json('编辑成功','100');
        }else{
            return $this->return_json('编辑失败','0');
        }
    }

    /*
     * 实例化模型
     * */
    public function new_model()
    {
        if($this->model_name != null){
            $name = "\\app\\index\\model\\{$this->model_name}";
            $this->model = new $name;
        }
    }

    /*
     * 公共验证规则
     * */
    public function checkDate($data)
    {
        return $this->validate->check($data);
    }


    /*
     * 返回json数据
     * $msg（提示信息）
     * $code（状态码）
     * */
    public function return_json($msg,$code){
        return json([
            'msg' => $msg,
            'code' => $code
        ]);
    }

    /*
     * 查询插件目录是否存在
     * */
    public function is_dir($name){
        $file = substr(__DIR__,0,23).'app/apply/controller/'.$name;
        View::assign('file',$file);

    }


    /*
     * 统计日期价格加入数据库
     * */
    public function time_price(){
        $list = Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price')
            ->join(['layout'=>'b'],'a.type_id=b.id')
            ->select();
        foreach($list as $v) {
            $data = [
                'monday' => $v['price'],
                'tuesday' => $v['price'],
                'wednesday' => $v['price'],
                'thursday' => $v['price'],
                'friday' => $v['price'],
                'saturday' => $v['price'],
                'sunday' => $v['price'],
                'eight' => $v['price'],
                'nine' => $v['price'],
                'ten' => $v['price'],
                'eleven' => $v['price'],
                'twelve' => $v['price'],
                'thirteen' => $v['price'],
                'fourteen' => $v['price'],
                'layout_id' => $v['id'],
                'create_time' => date("Ymd", time()),
            ];
            //不存在时候添加价格，
            if(!$this->select_find('week',['layout_id' => $v['id']])){
                Db::name('week')->save($data);
            }
        }
    }

    /*
     * 过期自动更新日期数据
     * */
    public function upd_price(){
        $week = $this->select_all('week');
        $list = Db::table('room')
            ->alias('a')
            ->field('a.*,b.type_name,b.price')
            ->join(['layout'=>'b'],'a.type_id=b.id')
            ->select();
        foreach ($week as $v){
            $time = date('Ymd',time()) - $v['create_time'];
            if($time){

                if($time = 1 ){
/*                    $data = [
                        'monday' => $v['tuesday'],
                        'tuesday' => $v['wednesday'],
                        'wednesday' => $v['thursday'],
                        'thursday' => $v['friday'],
                        'friday' => $v['saturday'],
                        'saturday' => $v['sunday'],
                        'sunday' => $v['eight'],
                        'eight' => $v['nine'],
                        'nine' => $v['ten'],
                        'ten' => $v['eleven'],
                        'eleven' => $v['twelve'],
                        'twelve' => $v['thirteen'],
                        'thirteen' => $v['fourteen'],
                        'fourteen' => $list[0]['price'],
                        'layout_id' => $v['id'],
                        'create_time' => date("Ymd", time()),
                    ];*/
                    $this->upd_data($v['tuesday'],$v['wednesday'],$v['thursday'],$v['friday'],$v['saturday'],$v['sunday'],$v['eight'],$v['nine'],$v['ten'],$v['eleven'], $v['twelve'],$v['thirteen'],$v['fourteen'],$list[0]['price'],$v['id']);
                }elseif ($time = 2){
                    $this->upd_data($v['wednesday'],$v['thursday'],$v['friday'],$v['saturday'],$v['sunday'],$v['eight'],$v['nine'],$v['ten'],$v['eleven'],$v['twelve'],$v['thirteen'],$v['fourteen'],$list[0]['price'],$list[0]['price'],$v['id']);
                }elseif ($time = 3){
                    $this->upd_data($v['thursday'],$v['friday'],$v['saturday'],$v['sunday'],$v['eight'],$v['nine'],$v['ten'],$v['eleven'],$v['twelve'],$v['thirteen'],$v['fourteen'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$v['id']);
                }elseif ($time = 4){
                    $this->upd_data($v['friday'],$v['saturday'],$v['sunday'],$v['eight'],$v['nine'],$v['ten'],$v['eleven'],$v['twelve'],$v['thirteen'],$v['fourteen'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$v['id']);
                }elseif ($time = 5){
                    $this->upd_data($v['saturday'],$v['sunday'],$v['eight'],$v['nine'],$v['ten'],$v['eleven'],$v['twelve'],$v['thirteen'],$v['fourteen'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$v['id']);
                }elseif ($time = 6){
                    $this->upd_data($v['sunday'],$v['eight'],$v['nine'],$v['ten'],$v['eleven'],$v['twelve'],$v['thirteen'],$v['fourteen'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$v['id']);
                }elseif ($time = 7){
                    $this->upd_data($v['eight'],$v['nine'],$v['ten'],$v['eleven'],$v['twelve'],$v['thirteen'],$v['fourteen'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$list[0]['price'],$v['id']);
                }elseif ($time = 8){

                }elseif ($time = 9){

                }elseif ($time = 10){

                }elseif ($time = 11){

                }elseif ($time = 12){

                }elseif ($time = 13){

                }else{

                }

            }
        }
    }

    /*
     * 要更新的数据
     * */
    public function upd_data($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13,$v14,$id){
        $data = [
            'monday' => $v1,
            'tuesday' => $v2,
            'wednesday' => $v3,
            'thursday' => $v4,
            'friday' => $v5,
            'saturday' => $v6,
            'sunday' => $v7,
            'eight' => $v8,
            'nine' => $v9,
            'ten' => $v10,
            'eleven' => $v11,
            'twelve' => $v12,
            'thirteen' => $v13,
            'fourteen' => $v14,
//            'layout_id' => $id,
            'create_time' => date("Ymd", time()),
        ];
        if(Db::name('week')->where('id',$id)->update($data)){
            dump('更新成功');
        }else{
            dump('更新失败');
        }

    }

}
