<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\service;

use app\admin\model\Menu;
use app\common\service\BaseService;

/**
 * 菜单管理-服务类
 * @author 牧羊人
 * @since 2020/7/10
 * Class MenuService
 * @package app\admin\service
 */
class MenuService extends BaseService
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new Menu();
    }

    /**
     * 获取数据列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @since: 2020/7/10
     * @author 牧羊人
     */
    public function getList()
    {
        $list = $this->model->getList([], 'id asc');
        if ($list) {
            foreach ($list as &$val) {
                if (intval($val['type']) <= 2) {
                    $val['open'] = true;
                } else {
                    $val['open'] = false;
                }
            }
        }
        return message("操作成功", true, $list);
    }

    /**
     * 添加或编辑
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function edit()
    {
        // 请求参数
        $data = request()->param();
        $result = $this->model->edit($data);
        if (!$result) {
            return message("操作失败", false);
        }
        // 节点参数
        $func = isset($data['func']) ? $data['func'] : "";
        // URL地址
        $url = trim($data['url']);
        if ($data['type'] == 3 && $func && $url) {
            $item = explode("/", $url);
            if (count($item) == 3) {
                // 模块名
                $module = $item[1];
                $funcList = explode(",", $func);
                foreach ($funcList as $val) {
                    $data = [];
                    if ($val == 1) {
                        // 列表
                        $data = [
                            'name' => "列表",
                            'url' => "/{$module}/list",
                            'permission' => "sys:{$module}:list",
                            'pid' => $result,
                            'type' => 4,
                            'status' => 1,
                            'is_public' => 2,
                            'sort' => $val,
                        ];
                    } else if ($val == 5) {
                        // 添加
                        $data = [
                            'name' => "添加",
                            'url' => "/{$module}/edit",
                            'permission' => "sys:{$module}:add",
                            'pid' => $result,
                            'type' => 4,
                            'status' => 1,
                            'is_public' => 2,
                            'sort' => $val,
                        ];
                    } else if ($val == 10) {
                        // 修改
                        $data = [
                            'name' => "修改",
                            'url' => "/{$module}/edit",
                            'permission' => "sys:{$module}:edit",
                            'pid' => $result,
                            'type' => 4,
                            'status' => 1,
                            'is_public' => 2,
                            'sort' => $val,
                        ];
                    } else if ($val == 15) {
                        // 删除
                        $data = [
                            'name' => "删除",
                            'url' => "/{$module}/drop",
                            'permission' => "sys:{$module}:drop",
                            'pid' => $result,
                            'type' => 4,
                            'status' => 1,
                            'is_public' => 2,
                            'sort' => $val,
                        ];
                    } else if ($val == 20) {
                        // 详情
                        $data = [
                            'name' => "详情",
                            'url' => "/{$module}/detail",
                            'permission' => "sys:{$module}:detail",
                            'pid' => $result,
                            'type' => 4,
                            'status' => 1,
                            'is_public' => 2,
                            'sort' => $val,
                        ];
                    } else if ($val == 25) {
                        // 状态
                        $data = [
                            'name' => "状态",
                            'url' => "/{$module}/setStatus",
                            'permission' => "sys:{$module}:status",
                            'pid' => $result,
                            'type' => 4,
                            'status' => 1,
                            'is_public' => 2,
                            'sort' => $val,
                        ];
                    } else if ($val == 30) {
                        // 批量删除
                        $data = [
                            'name' => "批量删除",
                            'url' => "/{$module}/batchDrop",
                            'permission' => "sys:{$module}:batchDrop",
                            'pid' => $result,
                            'type' => 4,
                            'status' => 1,
                            'is_public' => 2,
                            'sort' => $val,
                        ];
                    }
                    $menuMod = new Menu();
                    $menuMod->edit($data);
                }
            }
        }
        return message();
    }

    /**
     * 获取导航菜单
     * @param $permission
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since: 2020/7/10
     */
    public function getNavbarMenu($permission)
    {
        $list1 = [];
        $list2 = [];
        $list3 = [];

        foreach ($permission as $key => $val) {
            if (count($val) <= 0) {
                continue;
            }

            //查看节点状态
            if (is_array($val)) {
                $funcIds = implode(',', $val);
                $funcNum = $this->model->where([
                    'id' => array('in', $funcIds),
                    'status' => 1,
                ])->count();
                if ($funcNum <= 0) {
                    continue;
                }
            }

            $item = [];
            do {
                $info = $this->model->getInfo($key);
                if ($info && $info['status'] == 1) {
                    $item[] = $info;
                    $key = isset($info['pid']) ? (int)$info['pid'] : 0;
                } else {
                    $key = 0;
                }
            } while ($key > 0);
            if (is_array($item) && count($item) > 0) {
                $result = array_reverse($item);

                $item1 = isset($result[0]) ? $result[0] : [];
                $item2 = isset($result[1]) ? $result[1] : [];
                $item3 = isset($result[2]) ? $result[2] : [];

                if (getter($item1, 'id')) {
                    $list1[$item1['id']] = $item1;
                    if (getter($item2, 'id')) {
                        $list2[$item1['id']][$item2['id']] = $item2;
                    }
                    if (getter($item3, 'id')) {
                        $list3[$item2['id']][$item3['id']] = $item3;
                    }
                }
            }
        }
        unset($key);
        unset($val);

        $list = [];

        // 菜单处理
        foreach ($list1 as $key => &$val) {
            $menuList2 = isset($list2[$key]) ? $list2[$key] : [];
            if (!is_array($menuList2)) {
                continue;
            }
            foreach ($menuList2 as $kt => &$vt) {
                $menuList3 = isset($list3[$kt]) ? $list3[$kt] : [];
                if (!is_array($menuList3)) {
                    continue;
                }
                $menuList3 = array_merge($menuList3, array());
                $vt['children'] = $menuList3;
            }
            $menuList2 = array_merge($menuList2, array());
            $val['children'] = $menuList2;
            $list[] = $val;
        }
        $list = array_merge($list, array());

        return message("操作成功", true, $list);
    }
}
