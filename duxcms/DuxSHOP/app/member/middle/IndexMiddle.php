<?php

/**
 * 会员中心
 */

namespace app\member\middle;


class IndexMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];

    protected function meta($title = '会员中心', $name = '会员中心') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => $name,
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();
        return $this->config;
    }


    protected function data() {
        $this->config = $this->getConfig();
        $userInfo = $this->params['user_info'];
        $platform = $this->params['platform'];

        $return = [];
        if($platform == 'web') {
            $hookList = hook('service', 'html', 'MemberIndexBody', [$userInfo]);

            $data = [];
            foreach ((array)$hookList as $value) {
                $data = array_merge_recursive((array)$data, (array)$value);
            }
            $data = array_sort($data, 'sort');
            $hookHtml = '';
            foreach ($data as $app => $vo) {
                if (!empty($vo)) {
                    $hookHtml .= $vo['html'];
                }
            }

            $hookSideList = hook('service', 'html', 'MemberIndexSide', [$userInfo]);
            $data = [];
            foreach ((array)$hookSideList as $value) {
                $data = array_merge_recursive((array)$data, (array)$value);
            }
            $data = array_sort($data, 'sort');
            $hookSideHtml = '';
            foreach ($data as $app => $vo) {
                if (!empty($vo)) {
                    $hookSideHtml .= $vo['html'];
                }
            }
            $return = [
                'bodyHookHtml' => $hookHtml,
                'sideHookHtml' => $hookSideHtml
            ];
        }

        if($platform == 'mobile') {
            $hookList = hook('service', 'html', 'MemberIndexMobile', [$userInfo]);
            $data = [];
            foreach ((array)$hookList as $value) {
                $data = array_merge_recursive((array)$data, (array)$value);
            }
            $data = array_sort($data, 'sort');
            $hookHtml = '';
            foreach ($data as $app => $vo) {
                if (!empty($vo)) {
                    $hookHtml .= $vo['html'];
                }
            }
            $hookAfterList = hook('service', 'html', 'MemberIndexAfterMobile', [$userInfo]);
            $data = [];
            foreach ((array)$hookAfterList as $value) {
                $data = array_merge_recursive((array)$data, (array)$value);
            }
            $data = array_sort($data, 'sort');
            $hookAfterHtml = '';
            foreach ($data as $app => $vo) {
                if (!empty($vo)) {
                    $hookAfterHtml .= $vo['html'];
                }
            }
            $list = hook('service', 'menu', 'MobileMember');
            $menuList = [];
            foreach ((array)$list as $value) {
                $menuList = array_merge_recursive((array)$menuList, (array)$value);
            }
            $menuList = array_sort($menuList, 'order', 'asc', true);
            foreach ($menuList as $app => $appList) {
                $menuList[$app]['menu'] = array_sort($appList['menu'], 'order', 'asc');
            }


			$realInfo = target('member/MemberReal')->getWhereInfo([
			    'A.user_id' => $userInfo['user_id'],
                'A.status' => 2
            ]);

            $return = [
                'menuList' => $menuList,
                'hookHtml' => $hookHtml,
                'hookAfterHtml' => $hookAfterHtml,
                'realInfo' => $realInfo
            ];
        }

        return $this->run($return);
    }

    protected function about() {
        $this->config = $this->getConfig();
        return $this->run([
            'content' => html_out($this->config['reg_info'])
        ]);
    }

}