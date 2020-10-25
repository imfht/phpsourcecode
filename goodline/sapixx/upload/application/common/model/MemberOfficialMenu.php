<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信自定义菜单管理
 * 微信认证权限表 Table<ai_member_official_menu>
 */
namespace app\common\model;
use think\Model;
use category\Tree;

class MemberOfficialMenu extends Model{

    protected $pk    = 'id';

    //添加或编辑
    public function edit($param){
        $data['name']      = $param['name'];
        $data['sort']      = $param['sort'];
        $data['update_time']  = time();
        if(isset($param['id'])){
            return self::update($data,['id'=>(int)$param['id']]);
        }else{
            $data['parent_id'] = $param['parent_id'];
            return self::insert($data);
        }
    } 

    /**
     * 菜单修正
     * @param [type] $data
     * @return void
     */
    public static function official_menu($appid){
        $official_menu = self::where(['member_miniapp_id' => $appid])->order('sort asc,id desc')->select(); 
        $menu = [];
        $i = 0;
        //类型判断
        foreach ($official_menu as $value) {
            $i++;
            $menu[$i]['id']        = $value['id'];
            $menu[$i]['parent_id'] = $value['parent_id'];
            $menu[$i]['type']      = $value['types'];
            $menu[$i]['name']      = $value['name'];
            switch ($value['types']) {
                case 'click':
                    $menu[$i]['key']  = $value['key'];
                    break;
                case 'miniprogram':
                    $menu[$i]['url']      = $value['url'];
                    $menu[$i]['appid']    = $appid;
                    $menu[$i]['pagepath'] = $value['pagepath'];
                    break;
                default:
                    $menu[$i]['url']      = $value['url'];
                    break;
            }
        }
        //重新排序
        $mpmenu = [];
        $i = 0;
        foreach ($menu as $value) {
            if($value['parent_id'] == 0){
                $val = $value;
                unset($val['id']);
                unset($val['parent_id']);
                $mpmenu[$i] = $val;
                foreach ($menu as $k => $v) {
                    $values = $v;
                    unset($values['id']);
                    unset($values['parent_id']);
                    if($v['parent_id'] == $value['id']){
                        $mpmenu[$i]['sub_button'][] = $values;
                    }
                }
                $i++;
            }
        }
        return $mpmenu;
    }
}