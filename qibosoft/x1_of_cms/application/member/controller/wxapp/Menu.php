<?php
namespace app\member\controller\wxapp;

use app\common\controller\MemberBase;
use app\common\util\Menu AS MenuModel;

class Menu extends MemberBase
{
    public function get()
    {
        $menu_array = MenuModel::make('member');
        foreach($menu_array AS $key1=>$rs1){
            foreach($rs1['sons'] AS $key2=>$rs2){
                if ($key1=='often') {
                    continue;
                }elseif($key1=='base'){
                    $webdb = $this->webdb;
                }elseif($key1=='plugin'){
                    $webdb = $this->webdb['P__'.$rs2['dirname']];
                }elseif($key1=='module'){
                    $webdb = $this->webdb['M__'.$rs2['dirname']];
                }else{
                    $webdb = $this->webdb['M__'.$key1];
                }
                foreach($rs2['sons'] AS $key3=>$rs3){
                    //判断是否具有菜单权限
                    if ($rs3['power'] && $webdb[$rs3['power']] && empty(in_array($this->user['groupid'], $webdb[$rs3['power']]))) {
                        unset($menu_array[$key1]['sons'][$key2]['sons'][$key3]);    //没权限,直接隐藏
                    }
                }
                if (count($menu_array[$key1]['sons'][$key2]['sons'])<1) {   //没有子菜单,就把父菜单隐藏
                    unset($menu_array[$key1]['sons'][$key2]);
                }
            }
            if (count($menu_array[$key1]['sons'])<1) {   //没有子菜单,就把父菜单隐藏
                unset($menu_array[$key1]);
            }
        }
        return $this->ok_js($menu_array);
    }
}
