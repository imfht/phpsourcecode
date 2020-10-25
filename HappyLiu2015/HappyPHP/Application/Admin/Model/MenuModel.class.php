<?php
/**
 * Created by PhpStorm.
 * $$Id User: Administrator Date: 15-11-20 Time: 下午4:44
 */

namespace Admin\Model;

use Think\Model;

class MenuModel extends Model {
    /**
     * 获取一级菜单
     *
     * @return mixed
     */
    public function getTopMenu() {
        return $this->where('pid = 0')->order('sort desc')->select();
    }

    /**
     * 获取权限节点菜单
     *
     * @return mixed
     */
    public function getMenuList() {
        $menus = $this->order('sort desc')->select();
        // 格式化菜单为BUI格式的数组
        $menus = $this->format_menu($menus);

        // 返回菜单
        return $menus;
    }

    /**
     * 格式化菜单符合框架所需要的JSON格式
     *
        $menus = array(
            array(
                'id' => 'home',
                'homePage' => 'main',
                'menu' => array(
                    array(
                        'text' => '系统相关',
                        'items' => array(
                            array(
                                'id' => 'main',
                                'text' => '后台首页',
                                'href' => __MODULE__.'/index/main',
                                'closeable' => false,
                            ),
                            array(
                                'id' => 'menu',
                                'text' => '菜单管理',
                                'href' => __MODULE__.'/menu/index',
                            )
                        )
                    ),
                    array(
                        'text' => '用户操作',
                        'items' => array(
                            array(
                                'id' => 'codef',
                                'text' => '用户组',
                                'href' => __MODULE__.'/index/main',
                            ),
                            array(
                                'id' => 'codee1',
                                'text' => '权限管理',
                                'href' => __MODULE__.'/index/main',
                            )
                        )
                    )
                ),
            ),
            array(
                'id' => 'order',
                'homePage' => 'order1',
                'menu' => array(
                    array(
                        'text' => '菜单分组一',
                        'items' => array(
                            array(
                                'id' => 'order1',
                                'text' => '首页代码',
                                'href' => __MODULE__.'/index/main',
                            ),
                            array(
                                'id' => 'order2',
                                'text' => '首页主页',
                                'href' => __MODULE__.'/index/main',
                            )
                        )
                    )
                ),
            ),
        );
     * @param array $menu
     * @param int $pid
     * @return array
     */
    public function format_menu($menu=array(), $pid=0) {
        $ret = array();
        if($menu) {
            foreach($menu as $val) {
                if($val['pid'] == $pid) {
                    if($val['level'] == 1) {
                        $val['menu'] = $this->format_menu($menu, $val['id']);
                        $val['id'] = $val['tag'];
                        $val['homePage'] = $val['homepage'];
                        unset($val['text'], $val['homepage'], $val['href'], $val['closeable']);
                    } elseif($val['level'] == 2) {
                        $val['items'] = $this->format_menu($menu, $val['id']);
                        $val['id'] = $val['tag'];
                        unset($val['tag'], $val['icon'], $val['sort'], $val['add_time'], $val['pid'], $val['homepage'], $val['href'], $val['closeable']);
                    } else {
                        $val['href'] = __MODULE__.$val['href'];
                        $val['child'] = $this->format_menu($menu, $val['id']);
                        $val['id'] = $val['tag'];
                        $val['closeable'] = (bool)$val['closeable'];
                        unset($val['tag'], $val['icon'], $val['homepage'], $val['sort'], $val['add_time'], $val['pid']);
                    }
                    unset($val['sort'], $val['add_time'], $val['pid']);

                    $ret[] = $val;
                }
            }
        }
        return $ret;
    }

} 