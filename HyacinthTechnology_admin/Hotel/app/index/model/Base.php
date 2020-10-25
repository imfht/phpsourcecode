<?php
declare (strict_types = 1);

namespace app\index\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Base extends Model
{
    /*
     * 查询数据
     * */
    public function  select_plus($types = 1){
        if($types == 'page'){
            return self::order('id', 'asc')->paginate(10);
        }elseif($types == 'all'){
            return self::order('id', 'asc')->select();
        }

    }

    /*
     * 2张表，链表操作
     * */
    public function two_join($table,$field,$where,$map = null,$types = null){

        if($types == null){
            return self::alias('a')
                    ->field($field)
                    ->join($table.' b',$where)
                    ->paginate(10);
//            echo self::getLastSql();
        }
    }

    /*
     * 添加数据
     * */
    public function  add_plus(){

        if(request()->isAjax()){
            $data = input('param.');
            $data['create_time'] = time();
            //判断是否添加成功
            if(self::save($data)){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }
    }

    /*
     * 删除数据
     * */
    public function delete_plus(){
        if(request()->isAjax()){
            //判断是否删除成功
            if(self::where('id',input('id'))->delete()){
                return $this->return_json('删除成功','100');
            }else{
                return $this->return_json('删除失败','0');
            }
        }
    }

    /*
     * 编辑数据
     * */
    public function edit_plus(){
        if(self::update(input('param.'))){
            return $this->return_json('编辑成功','100');
        }else{
            return $this->return_json('编辑失败','0');
        }
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
}
