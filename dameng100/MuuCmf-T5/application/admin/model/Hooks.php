<?php
namespace app\admin\model;

use think\Model;

/**
 * 插件模型
 */

class Hooks extends Model {

    /**
     * 查找后置操作
     */
    protected function _after_find(&$result,$options) {

    }

    protected function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }

    /**
     * 编辑/新增数据
     *
     * @param      <type>  $data   The data
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function editData($data)
    {   
        $data['status'] = 1; //默认状态为1

        if(!empty($data['id'])){
            $res = $this->allowField(true)->save($data,$data['id']);
        }else{
            $res = $this->allowField(true)->save($data);
        }
        
        if($res) $res = $this->id;

        return $res;
    }

    /**
     * 更新插件里的所有钩子对应的插件
     */
    public function updateHooks($addons_name){
        $addons_class = get_addon_class($addons_name);//获取插件名
        if(!class_exists($addons_class)){
            $this->error = "未实现{$addons_name}插件的入口文件";
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = collection($this->column('name'))->toArray();

        $common = array_intersect($hooks, $methods);

        if(!empty($common)){
            foreach ($common as $hook) {
                $flag = $this->updateAddons($hook, array($addons_name));
                if(false === $flag){
                    $this->removeHooks($addons_name);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 更新单个钩子处的插件
     * @param  [type] $hook_name   插件执行方法
     * @param  [type] $addons_name 插件名||插件目录
     * @return [type]              [description]
     */
    public function updateAddons($hook_name, $addons_name){
        $o_addons = $this->where(['name'=>$hook_name])->column('addons');
        $o_addons = $o_addons[0];
        $o_addons = explode(',',$o_addons);
        if(!empty($o_addons[0]) || $o_addons[0]!=''){

            $addons = array_merge($o_addons, $addons_name);
            $addons = array_unique($addons);
        }else{
            $addons = $addons_name;
        }
        $flag = $this->where(['name'=>$hook_name])->setField('addons',implode(',',$addons));
        if(false === $flag)
            $this->where(['name'=>$hook_name])->setField('addons',implode(',',$o_addons));
        return $flag;
    }

    /**
     * 去除插件所有钩子里对应的插件数据
     */
    public function removeHooks($addons_name){
        $addons_class = get_addon_class($addons_name);
        if(!class_exists($addons_class)){
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = collection($this->column('name'))->toArray();

        $common = array_intersect($hooks, $methods);
        if($common){
            foreach ($common as $hook) {
                $flag = $this->removeAddons($hook, array($addons_name));
                if(false === $flag){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 去除单个钩子里对应的插件数据
     */
    public function removeAddons($hook_name, $addons_name){
        $o_addons = $this->where(['name'=>$hook_name])->column('addons');
        $o_addons = $o_addons[0];
        $o_addons = explode(',',$o_addons);
        
        if(!empty($o_addons[0]) || $o_addons[0]!=''){
            $addons = array_diff($o_addons, $addons_name);
        }else{
            return true;
        }
        $flag = $this->where(['name'=>$hook_name])->setField('addons',implode(',',$addons));
        if(false === $flag)
            $this->where(['name'=>$hook_name])->setField('addons',implode(',',$o_addons));
        return $flag;
    }
}
