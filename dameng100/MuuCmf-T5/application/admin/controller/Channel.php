<?php
namespace app\admin\Controller;

use app\admin\controller\Admin;
use think\Db;
/**
 * 后台频道控制器
 */

class Channel extends Admin
{

    /**
     * 频道列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {
        $Channel = Db::name('Channel');
        if (request()->isPost()) {

            $one = $_POST['nav'][1];
            if (count($one) > 0) {
                Db::execute('TRUNCATE TABLE ' . config('database.prefix') . 'channel');

                for ($i = 0; $i < count(reset($one)); $i++) {
                    $data[$i] = array(
                        'pid' => 0,
                        'title' => html($one['title'][$i]),
                        'url' => text($one['url'][$i]),
                        'sort' => intval($one['sort'][$i]),
                        'target' => empty($one['target'][$i]) ? 0:intval($one['target'][$i]),
                        'band_text' => text($one['band_text'][$i]),
                        'band_color' => text($one['band_color'][$i]),
                        'status' => 1
                    );
                    $pid[$i] = $Channel->insert($data[$i]);
                }

                if(!empty($_POST['nav'][2])){
                    $two = $_POST['nav'][2];

                    for ($j = 0; $j < count(reset($two)); $j++) {
                        $data_two[$j] = array(
                            'pid' => $pid[$two['pid'][$j]],
                            'title' => html($two['title'][$j]),
                            'url' => text($two['url'][$j]),
                            'sort' => intval($two['sort'][$j]),
                            'target' => intval($two['target'][$j]),
                            'band_text' => text($two['band_text'][$j]),
                            'band_color' => text($two['band_color'][$j]),
                            'status' => 1
                        );
                        $res[$j] = $Channel->insert($data_two[$j]);
                    }
                }
                
                cache('common_nav',null);
                $this->success(lang('_CHANGE_'));
            }
            $this->error(lang('_NAVIGATION_AT_LEAST_ONE_'));


        } else {
            /* 获取频道列表 */
            $map = array('status' => array('gt', -1), 'pid' => 0);
            $list = $Channel->where($map)->order('sort asc,id asc')->select();
            foreach ($list as $k => &$v) {
                $module = Db::name('Module')->where(array('entry' => $v['url']))->find();

                $v['module_name'] = $module['name'];
                $child = $Channel->where(array('status' => array('gt', -1), 'pid' => $v['id']))->order('sort asc,id asc')->select();
                foreach ($child as $key => &$val) {
                    $module = Db::name('Module')->where(array('entry' => $val['url']))->find();
                    $val['module_name'] = $module['name'];
                }
                unset($key, $val);
                $child && $v['child'] = $child;
            }
            unset($k, $v);

            $this->assign('module', $this->getModules());
            $this->assign('list', $list);

            $this->setTitle(lang('_NAVIGATION_MANAGEMENT_'));
            return $this->fetch();
        }

    }

    /**
     * 用户导航
     * @return [type] [description]
     */
    public function user(){
        $Channel = Db::name('UserNav');
        if (request()->isPost()) {
            $one = $_POST['nav'][1];
            if (count($one) > 0) {
                Db::execute('TRUNCATE TABLE ' . config('database.prefix') . 'user_nav');

                for ($i = 0; $i < count(reset($one)); $i++) {
                    $data[$i] = array(
                        'title' => text($one['title'][$i]),
                        'url' => text($one['url'][$i]),
                        'sort' => intval($one['sort'][$i]),
                        'target' => intval($one['target'][$i]),
                        //'color' => text($one['color'][$i]),
                        'band_text' => text($one['band_text'][$i]),
                        'band_color' => text($one['band_color'][$i]),
                        'status' => 1
                    );
                    $pid[$i] = $Channel->insert($data[$i]);
                }
                cache('common_user_nav',null);
                $this->success(lang('_CHANGE_'));
            }
            $this->error(lang('_NAVIGATION_AT_LEAST_ONE_'));


        } else {
            /* 获取频道列表 */
            $map = array('status' => array('gt', -1));
            $list = $Channel->where($map)->order('sort asc,id asc')->select();
            foreach ($list as $k => &$v) {
                $module = Db::name('Module')->where(array('entry' => $v['url']))->find();
                $v['module_name'] = $module['name'];
                unset($key, $val);
            }

            unset($k, $v);
            $this->assign('module', $this->getModules());
            $this->assign('list', $list);

            $this->setTitle(lang('_NAVIGATION_MANAGEMENT_'));
            return $this->fetch();
        }
    }

    private function getModules()
    {
        $result = model('common/Module')->getAll(['is_setup' => 1]);
        return $result;
    }


}
