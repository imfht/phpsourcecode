<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 18:31
 */

namespace app\admin\model;


use think\Model;

class GoodconfigModel extends Model
{

    protected $name="good_config";

    public function add($data){
        try {

            $setOne = $this->find();
            if($setOne){
                $this->save($data,['config_id'=>$setOne['config_id']]);
                return easymsg(1,url('goodconfig/index'),'修改成功！');
            }else{

                $this->save($data);
                return easymsg(1,url('goodconfig/index'),'添加成功！');
            }


        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }

    public function findByone(){
        return $this->find();
    }

}