<?php

/**
 * 菜单处理
 */
namespace app\system\model;

class MenuModel {

    /**
     * 菜单列表
     */
    public function loadList($config = []) {
        $module = $config['module'];
        $auth = $config['auth'];
        $list = hook('service', 'menu', 'system');
        $topKey = 'system';
        $parentKey = 'index';
        $breadCrumb = [];
        $data = array();
        foreach ((array)$list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }

        $curUrl = ROOT_URL . '/' . $module . '/';
		$rootName = ROLE_NAME;
        if(!empty($rootName)){
            $curUrl = ROOT_URL . '/' . ROLE_NAME . '/' .  $module . '/';
        }


        //遍历菜单
        $list = array();
        $data = array_sort($data, 'order', 'asc', true);
        foreach ($data as $app => $appList) {
            $appData = array(
                'name' => $appList['name'],
                'icon' => $appList['icon'],
                'order' => $appList['order']
            );
            if (empty($appList['menu'])) {
                continue;
            }
            $parentData = array();
            $appList['menu'] = array_sort($appList['menu'], 'order', 'asc');
            foreach ($appList['menu'] as $parent => $parentList) {
                $parentData[$parent] = array(
                    'name' => $parentList['name'],
                    'icon' => $parentList['icon'],
                    'order' => $parentList['order'],
                    'url' => ''
                );
                if (empty($parentList['menu'])) {
                    continue;
                }
                $subData = array();
                $parentList['menu'] = array_sort($parentList['menu'], 'order', 'asc');
                foreach ($parentList['menu'] as $sub => $subList) {
                    $urlAuth = $this->formatUrl($subList['url']);
                    if (!in_array(implode('.', $urlAuth), (array)$auth)) {
                        continue;
                    }
                    if(empty($parentData[$parent]['url'])) {
                        $parentData[$parent]['url'] = $subList['url'];
                    }
                    if(empty($appData['url'])) {
                        $appData['url'] = $subList['url'];
                    }
                    $subData[$sub] = array(
                        'name' => $subList['name'],
                        'icon' => $subList['icon'],
                        'url' => $subList['url'],
                        'order' => $subList['order']
                    );

                    if (strpos($subList['url'], $curUrl) !== false) {
                        $subData[$sub]['cur'] = true;
                        $parentData[$parent]['cur'] = true;
                        $appData['cur'] = true;
                        $topKey = $app;
                        $breadCrumb = [['name' => $appData['name'], 'url' => $parentData[$parent]['url']], ['name' => $parentData[$parent]['name'], 'url' => $parentData[$parent]['url']], ['name' => $subList['name'], 'url' => $subList['url']],];
                    }
                }
                if(empty($subData)) {
                    unset($parentData[$parent]);
                }else{
                    $parentData[$parent]['menu'] = $subData;
                }
                if(empty($parentData)) {
                    unset($parentData);
                }else{
                    $appData['menu'] = $parentData;
                }
            }
            if(!empty($appData['menu'])) {
                $list[$app] = $appData;
            }else {
                unset($list[$app]);
            }
        }
        return array('nav' => $list, 'breadCrumb' => $breadCrumb, 'aside' => (array)$list[$topKey]['menu']);
    }

    private function formatUrl($url) {
        $url = explode('.', str_replace(ROOT_URL, '', $url), 2);
        $url = array_filter(explode('/', str_replace('\\', '/', $url[0])));
        unset($url[1]);
        return $url;
    }

}