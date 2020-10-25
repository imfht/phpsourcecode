<?php

namespace Addons\ImageManager;
use Common\Controller\Addon;

/**
 * 图片管理插件
 * @author 凡人
 */

    class ImageManagerAddon extends Addon{

        public $info = array(
            'name'=>'ImageManager',
            'title'=>'图片管理',
            'description'=>'图片管理，快速选择已上传图片到封面',
            'status'=>1,
            'author'=>'凡人',
            'version'=>'0.1'
        );

        public function install(){
            if ($this->existHook("AdminPageFooter")) {
                $this->updateHookAddons("AdminPageFooter", "ImageManager");
            }else {
                $this->addHook("AdminPageFooter", "ImageManager", "后台钩子");
            }
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的AdminPageFooter钩子方法
        public function AdminPageFooter(){
            $this->assign("addon_path", $this->addon_path);
            $this->display("widget");
        }
		
        /**
         * 获取钩子详情
         * @param string $hook 钩子名称
         * @return array
         */
        public function getHooks ($hook) {
            $hook_mod = M('Hooks');
            $where['name'] = $hook;
            return $hook_mod->where($where)->find();
        }
        /**
         * 判断钩子是否存在
         * @param string $hook 钩子名称
         * @return boolean
         */
        public function existHook ($hook) {
            $hooks = $this->getHooks($hook);
            return !empty($hooks);
        }
        /**
         * 删除钩子
         * @param string $hook 钩子名称
         */
       public function deleteHook($hook){
           $hook_mod = M('hooks');
           $where = array(
               'name' => $hook,
           );
           $hook_mod->where($where)->delete();
       }

       /**
        * 新增钩子
        * @param string $hook   钩子名称
        * @param string $addons 插件名称
        * @param string $msg    钩子简介
        */
        public function addHook($hook, $addons = '', $msg = ''){
            $hook_mod = M('Hooks');
            $data['name']        = $hook;
            $data['description'] = $msg;
            $data['type']        = 1;
            $data['update_time'] = NOW_TIME;
            $data['addons']      = $addons;
            if( false !== $hook_mod->create($data) ){
                $hook_mod->add();
            }
        }

        /**
         * 更新钩子的插件字段
         * @param type $hook
         * @param type $addons
         * @return boolean
         */
        public function updateHookAddons ($hook, $addons = '') {
            $hooks = $this->getHooks($hook);
            if (in_array($addons, explode(",", $hooks['addons']))) {
                return true;
            }

            $data['id']          = $hooks['id'];
            $data['update_time'] = NOW_TIME;
            $data['addons']      = empty($hooks['addons']) ? $addons : $hooks['addons'] . "," .$addons;
            $hook_mod = M('Hooks');
            $hook_mod->save($data);
        }
    }