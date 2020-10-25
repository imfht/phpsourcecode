<?php
namespace app\admin\model;
use think\Model;
/**
 * 网站配置
 */
class Config extends Model {
    /**
     * 获取信息
     * @return array 网站配置
     */
    public function getInfo($where=array(),$field='*'){
        $list = $this->field($field)->where($where)->select();
        $config = array();
        foreach ($list as $key => $value) {
            $config[$value['name']] = $value['data'];
        }
        return $config;
    }
    /**
     * 更新
     */
    public function edit(){
        $data = input('post.');
        if(empty($data)){
            $this->error = '数据创建失败！';
            return false;
        }
        $data=input('post.');
        foreach ($data as $name => $value) {
            $map = array('name' => $name);
            $status=$this->where($map)->setField('data', $value);
            if($status === false){
                return false;
            }
        }
        return true;
    }

    /**
     * 获取当前模板文件
     * @return array 文件列表
     */
    public function tplList()
    {
        $config = $this->getInfo();
        $tplDir = ROOT_PATH .'public/'. THEME_NAME . '/' . $config['tpl_name'];
        if(!is_dir($tplDir)){
            return false;
        }
        $listFile=scandir($tplDir);
        if(is_array($listFile)){
            $list=array();
            foreach ($listFile as $key => $value) {
                if ($value != "." && $value != "..") {
                    $list[$key]['file']=$value;
                    $list[$key]['name']=substr($value, 0, -5);
                }
            }
        }
        return $list;
    }

    /**
     * 获取模板路径
     * @return array 主题列表
     */
    public function themesList()
    {
        $tplDir = __ROOT__ . THEME_NAME;
        if(!is_dir($tplDir)){
            return false;
        }
        $listFile=scandir($tplDir);
        if(is_array($listFile)){
        $list=array();
            foreach ($listFile as $key => $value) {
                if ($value != "." && $value != ".."&&!strpos($value,".")) {
                    $list[$key]['file']=$value;
                    $list[$key]['name']=$value;
                }
            }
        }
        return $list;
    }


}
