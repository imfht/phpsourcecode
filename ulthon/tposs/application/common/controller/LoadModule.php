<?php

namespace app\common\controller;

use think\Controller;

/**
 * 加载模块控制器
 * 
 * @var mixed
 */
class LoadModule extends Controller 
{
    /**
     * _initialize
     * 检测系统功能
     * 
     * 如果只是stroage，
     * 那么通过access key检测权限
     * 如果有system admin 功能，
     * 那么通过app 和 key检测权限
     * 如果有user admin功能
     * 那么通过auth检测权限
     * 如果开启了集群存储，那么把访问stroage的全部访问到clusters
     * @return mixed 
     */
    public function _initialize(){
        $module_list = \config('extend_modules');

        if(in_array('storage',$module_list) && !\in_array('system_admin',$module_list) && !\in_array('user_admin',$module_list)){
            $this->checkByAccessKey();
        }
    }

    /**
     * checkByAccessKey
     * 检测access key
     * 并检测是否开启公共读
     * @return mixed 
     */
    public function checkByAccessKey()
    {
        $access_key = \config('storage.access_key');
        $post_key = input('access_key');
        $get_premission = config('storage.get_permission');
        $action = \request()->action();
        if($action == 'get' || $action == 'index'){
            if(!$get_premission){
                $this->error('无权限');
            }
            if($post_key != $access_key){
                $this->error('无权限'); 
            }
        }else{
            if($post_key != $access_key){
                $this->error('无权限'); 
            }
        }
    }

    /**
     * checkByAppKey
     * 验证app的key
     * 并根据app的配置的权限读取文件
     * @return mixed 
     */
    public function checkByAppKey(){


        
    }

    /**
     * checkByAuth
     * 如果是多用户系统，那么根据用户验证帐号权限
     * 根据用户的app的配置的权限读取文件
     * @return mixed 
     */
    public function checkByAuth(){

    }
    /**
     * storageToClusters
     * 把对files的请求转到clusters
     * @return mixed 
     */
    public function storageToClusters(){


    }
}
