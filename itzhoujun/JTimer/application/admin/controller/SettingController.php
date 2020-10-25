<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/2
 * Time: 10:00
 */

namespace app\admin\controller;


use think\Db;
use think\Request;

class SettingController extends AdminBaseController
{

    public function index()
    {
        $configs = Db::name('setting')->order('`group` asc,sort asc')->select();
        $group_names = [
            1 => '系统设置',
            2 => '日志设置',
        ];
        $groups = [];
        foreach ($configs as $config){

            if($config['type'] == 3){
                $options = explode("\n",$config['options']);

                foreach ($options as &$item){
                    list($value,$name) = explode(':',$item);
                    $item = [
                        'name' => trim($name),
                        'value' => $value,
                    ];
                }
                $config['options'] = $options;
            }
            $groups[$config['group']][] = $config;
        }

        $this->assign('groups',$groups);
        $this->assign('group_names',$group_names);
        return $this->fetch();
    }

    public function postSetting(Request $request){
        $params = $request->post();
        foreach ($params as $name => $value){
            Db::name('setting')->where('name',$name)->update(['value' => $value]);
            cache('jtimer_setting_'.$name,null);
        }
        $this->success('更新成功');
    }
}