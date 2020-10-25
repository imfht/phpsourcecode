<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/18 0018
 * Time: 下午 7:51
 */
namespace Admin\Controller ;

use Admin\Builder\AdminConfigBuilder;

class MarkDownController extends AdminController{
    public function config() {
        $group = array() ;
        $builder = new AdminConfigBuilder() ;
        $data = $builder->handleConfig();
        $builder->data($data);
        $builder->title('MarkDown配置信息')->suggest('请谨慎配置，容易导致编辑器无法使用。编辑器配置名需要以当前大写模块名开头，如动态中添加：WEIBO_ADD。');
        foreach ($data as $key=>$val) {
            $map = explode('_', $key) ;
            if(count($map) >= 2){
                if(!D('Module')->checkInstalled(ucfirst(strtolower($map[0])))){
                    continue ;
                }
            }
            if($key == 'DEFAULT')
                continue ;
            $tip = switch_name($key) ;
            if(!is_array($group[$tip[1][1]]))
                $group[$tip[1][1]] = array() ;
            array_push($group[$tip[1][1]], $key) ;
            if($tip[0]['url']){
                $tip[0]['url'] = '位置：'.$tip[0]['url'] ;
            }else{
                $tip[0]['url'] = '' ;
            }
            $builder->keyTextarea($key, $tip[0]['tip'], $tip[0]['url']);
        }
        unset($val) ;
        $builder->keyTextarea('DEFAULT', '编辑器默认配置');
        if(isset($group['自定义编辑器'])){
            $builder->group('自定义编辑器', $group['自定义编辑器']);
            unset($group['自定义编辑器']) ;
        }
        if(isset($group['默认编辑器配置']))
            unset($group['默认编辑器配置']) ;
        $builder->group('编辑器默认配置', array('DEFAULT'));
        foreach ($group as $k=>$value){
            $builder->group($k, $value);
        }
        unset($value) ;
        $builder->buttonSubmit();
        $builder->display();
    }
}