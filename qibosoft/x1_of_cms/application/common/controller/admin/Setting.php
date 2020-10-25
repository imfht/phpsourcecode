<?php
namespace app\common\controller\admin;

use app\admin\controller\Setting AS _Setting;
use plugins\config_set\model\Group AS GroupModel;

//各频道参数设置
abstract class Setting extends _Setting
{
    //获得模块或插件ID，插件的话，取负数
    //abstract protected function getSysId();
    protected function getSysId(){
        preg_match_all('/([\w]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        if(defined('IN_PLUGIN')){
            $array = plugins_config($dirname);
            $id = -$array['id'];    //插件是负数
        }else{
            $array = modules_config($dirname);
            $id = $array['id'];
        }
        return $id;
    }
    
    /**
     * 参数设置
     * {@inheritDoc}
     * @see \app\admin\controller\Setting::index()
     */
    public function index($group=0){
        $array = $this->getNavIds();
        //只能管理自己的模块
        if (!in_array($group, $array)) {
            $group = current($array);
        }
        if(!$group){
            $data = [
                    'title'=>'基础设置',
                    'list'=>0,
                    'sys_id'=>$this->getSysId(),
                    'ifshow'=>0,
                    'ifsys'=>0,
            ];
            $result = GroupModel::create($data);
            if (empty($result)){
                $this->error('分类ID不存在!');
            }else{
                $group = $result->id;
            }            
        }
        return parent::index($group);
    }
    
    /**
     * 取得频道或插件 参数设置的分类数组
     * @return array
     */
    protected function getNavIds(){
        $array = GroupModel::getNavTitle(false,$this->getSysId());
        return array_flip($array);
    }
    
    /**
     * 获取模块里的分组设置
     * {@inheritDoc}
     * @see \app\admin\controller\Setting::setNav()
     */
    protected function setNav($group){
//         $this->tab_ext = [
//                 'nav'=>[
//                         GroupModel::getNav(false,$this->getSysId()),   //分组导航
//                         $group
//                 ],
//         ];
        $this->tab_ext['nav'] = [
                GroupModel::getNav(false,$this->getSysId()),   //分组导航
                $group
        ];
    }
    

}