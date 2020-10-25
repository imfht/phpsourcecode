<?php
declare (strict_types = 1);

namespace app\index\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Admin extends Base
{
    /*
     * 添加数据
     * */
    public function  add_plus(){

        if(request()->isAjax()){
            $data = input('param.');
            $data['create_time'] = time();
            $data['password'] = md5($data['password']);
            //判断是否添加成功
            if(self::save($data)){
                return $this->return_json('新增成功','100');
            }else{
                return $this->return_json('新增失败','0');
            }
        }
    }

    /*
     * 查询员工数据
     * */
    public function select_staff(){
        return self::alias('a')
            ->field('a.*,b.building')
            ->join('building b','a.building_id = b.id')
            ->paginate(10);

    }
}
