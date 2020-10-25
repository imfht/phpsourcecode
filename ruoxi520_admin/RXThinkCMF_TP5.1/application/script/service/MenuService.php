<?php


namespace app\script\service;

use app\script\model\Menu as MenuModel;
use app\script\model\Test;

class MenuService extends ScriptService
{
    /**
     * 初始化模型
     * @author zongjl
     * @date 2019/6/25
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new MenuModel();
    }

    /**
     * 更新菜单
     * @author zongjl
     * @date 2019/6/25
     */
    public function updateMenu()
    {
        $menu_model = new Test();
        $list = $menu_model->where([
            'pid' => 0,
            'mark' => 1,
        ])->order('sort', 'asc')->select()->toArray();
        if ($list) {
            foreach ($list as &$val) {
                $childs2 = $menu_model->where([
                    'pid' => $val['id'],
                    'mark' => 1,
                ])->order('sort', 'asc')->select()->toArray();
                $val['children'] = $childs2;

                if ($childs2) {
                    foreach ($val['children'] as &$val2) {
                        $childs3 = $menu_model->where([
                            'pid' => $val2['id'],
                            'mark' => 1,
                        ])->order('sort', 'asc')->select()->toArray();
                        $val2['children'] = $childs3;

                        if ($childs3) {
                            foreach ($val2['children'] as &$val3) {
                                $childs4 = $menu_model->where([
                                    'pid' => $val3['id'],
                                    'mark' => 1,
                                ])->order('sort', 'asc')->select()->toArray();
                                $val3['children'] = $childs4;
                            }
                        }
                    }
                }
            }
        }

        if ($list) {
            foreach ($list as $valItem) {
                unset($valItem['id']);
                $valItem['create_user'] = 1;
                $valItem['create_time'] = time();
                $menuId = $this->model->edit($valItem);
                if (!$menuId) {
                    continue;
                }
                foreach ($valItem['children'] as $val2Item) {
                    unset($val2Item['id']);
                    $val2Item['create_user'] = 1;
                    $val2Item['create_time'] = time();
                    $val2Item['pid'] = $menuId;
                    $menuId2 = $this->model->edit($val2Item);
                    if (!$menuId2) {
                        continue;
                    }

                    foreach ($val2Item['children'] as $val3Item) {
                        unset($val3Item['id']);
                        $val3Item['create_user'] = 1;
                        $val3Item['create_time'] = time();
                        $val3Item['pid'] = $menuId2;
                        $menuId3 = $this->model->edit($val3Item);
                        if (!$menuId3) {
                            continue;
                        }

                        foreach ($val3Item['children'] as $val4Item) {
                            unset($val4Item['id']);

                            // 重组URL
                            $item = explode('/', $val4Item['url']);
                            $url = "/" . strtolower($item[1]) . "/" . $item[2];
                            $val4Item['url'] = $url;

                            // 重组权限点
                            $subItem = explode(':', $val4Item['auth']);
                            $val4Item['auth'] = 'sys:' . strtolower($subItem[1]) . ':' . ($val4Item['name'] == '新增' ? 'add' : $subItem[2]);

                            $val4Item['create_user'] = 1;
                            $val4Item['create_time'] = time();
                            $val4Item['pid'] = $menuId3;
                            $this->model->edit($val4Item);
                            unset($val4Item);
                        }
                        unset($val3);
                    }
                    unset($val2);
                }
                unset($valItem);
            }
        }
    }

    public function menu()
    {
        $result = $this->model->getList([
            ['type', '=', 4],
        ]);
        if ($result) {
            foreach ($result as $val) {
                // 重组URL
                $item = explode('/', $val['url']);
                $url = "/" . strtolower($item[1]) . "/" . $item[2];
                $val['url'] = $url;

                // 重组权限点
                $subItem = explode(':', $val['auth']);
                $val['auth'] = 'sys:' . strtolower($subItem[1]) . ':' . ($val['name'] == '新增' ? 'add' : $subItem[2]);
                $this->model->edit($val);
            }
        }
    }

    public function test()
    {
        $result = $this->model->getList([
            ['type', '=', 3],
        ]);
        if ($result) {
            foreach ($result as $val) {
                $info = $this->model->getInfoByAttr([
                    ['pid', '=', $val['id']],
                ]);
                $url = getter($info, 'url');
                if (!$url) {
                    continue;
                }
                // 重组URL
                $item = explode('/', $url);

                $this->model->edit([
                    'id' => $val['id'],
                    'url' => strtolower($item[1]),
                ]);
            }
        }
    }
}
