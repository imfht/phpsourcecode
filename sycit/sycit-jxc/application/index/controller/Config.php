<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/8/20
// +----------------------------------------------------------------------
// | Title:  Config.php
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\index\model\BancaiList;
use app\index\model\MaterialSet;
use app\index\model\StorageCharge;
use think\Db;
use think\Request;
use think\Session;
use think\Url;

class Config extends Common_base
{
    public function index() {
        $data = Db::name('config')->select();
        $this->assign('data', $data);
        $this->assign('title', '基本配置');
        return $this->fetch();
    }
    // 保存
    public function edit() {
        // 设定数据返回格式
        \think\Config::set('default_return_type', 'json');
        // 是否有权限
        IS_ROOT(['1'])  ? true : $this->error('没有权限');

        if (Request::instance()->isPost()) {
            //
            foreach ($_POST AS $key=>$val) {
                $result = Db::name('config')->where(['name'=>$key])->setField('value', $val);
            }
            if ($result !== false) {
                Setting_Config(); // 写入缓存
                return $this->success('更新成功', Url::build('config/index'));
            } else {
                return $this->error('更新失败，你是否多做了些什么？', Url::build('config/index'));
            }
        }
    }

    //原料设定
    public function produce() {
        $lime = '25';
        $MaterialSet = new MaterialSet();
        $list = $MaterialSet->order('update_time desc')->paginate($lime);
        foreach ($list as $key => $val) {
            $list[$key]['son'] = [];
            if (!empty($val['ms_gl'])) {
                //转换数组
                $son = [];
                $exp = [];
                $blgl = explode(',', $val['ms_gl']);
                foreach ($blgl as $kb=>$vb) {
                    $exp[] = explode(':', $vb);
                }
                foreach ($exp as $ke=>$ve) {
                    $Charge = StorageCharge::get($ve[0]);
                    $son[] = [
                        'on' => $Charge['lxname'],
                        'val' => $ve[1],
                    ];
                    $list[$key]['son'] = $son;
                }
                unset($exp);
                unset($son);
            }
        }
        // 获取分页显示
        $page = $list->render();
        $assign = [
            'title' => '原料设定',
            'list' => $list,
            'page' => $page,
            'empty'=> '<tr><td colspan="7" align="center">当前条件没有查到数据</td></tr>'
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //添加设定
    public function produce_add() {
        // 是否有权限
        IS_ROOT(['1'])  ? true : $this->error('没有权限');
        $Request = Request::instance();

        if ($Request->isPost()) {
            //保存动作
            $ms_pnid = $Request->param('ms_pnid');
            $ms_blname = $Request->param('ms_blname');
            $ms_maname = $Request->param('ms_maname');
            $ms_baobian = $Request->param('ms_baobian');
            //查询是否已存在数据
            $byname = Db::name('material_set')->where('ms_pnid',$ms_pnid)
                ->where('ms_blname', $ms_blname)
                ->where('ms_maname', $ms_maname)
                ->where('ms_baobian', $ms_baobian)->find();
            if ($byname) {
                $this->error('数据库已存在原料设定');
            }

            $lx = $Request->param('liaox/a');
            $name = $Request->param('name/a');
            if (empty($ms_pnid) || empty($ms_blname) || empty($ms_maname) || empty($ms_baobian)) {
                $this->error('填写不完整');
            }
            if (!is_array($lx) && !is_array($name)) {
                $this->error('非法数据');
            }
            $ment = [];
            if (!empty($lx) && !empty($name)) {
                foreach ($lx as $klx=>$vlx) {
                    if (empty($name[$klx])) {
                        $this->error('对应数量不能为空');
                    }
                }
                foreach ($lx as $kl=>$vl) {
                    if ($vl == 'on') {
                        if(preg_match("/^[0-9]{1,2}+(.[5]{1})?$/",$name[$kl])){
                            $ment[] = $kl . ':' . $name[$kl];
                        } else {
                            $this->error('输入数值错误');
                        }
                    }
                }
            }

            if (empty($ment)) {
                $this->error('所关联的料型不能为空');
            } else {
                $MaterialSet = new MaterialSet();
                $MaterialSet->data([
                    'ms_pnid' => $ms_pnid,
                    'ms_blname' => $ms_blname,
                    'ms_maname' => $ms_maname,
                    'ms_baobian' => $ms_baobian,
                    'ms_gl' => join(',', $ment),
                    'ms_uid' => Session::get('user_id'),
                ]);
                $MaterialSet->save();
                $this->success('操作成功', Url::build('config/produce'));
            }
        } else {
            //系列
            $number = Db::name('product_number')->field('pn_name')->select();
            //板材
            $bancai = Db::name('bancai_list')->field('blname')->select();
            //属性
            $attribute = Db::name('material_att')->select();
            //料型
            $liaox = Db::name('storage_charge')->field('lxid,lxxhao,lxname')->order('lxid asc')->select();
            $assign = [
                'title' => '增加设定',
                'liaox' => $liaox,
                'number' => $number,
                'bancai' => $bancai,
                'attribute' => $attribute,
            ];
            $this->assign($assign);
            return $this->fetch();
        }
    }

    //修改设定
    public function produce_edit() {
        // 是否有权限
        IS_ROOT(['1'])  ? true : $this->error('没有权限');

        $Request = Request::instance();
        $pid = $Request->param('pid');

        if (!MaterialSet::get($pid)) {
            $this->error('参数错误');
        }

        if ($Request->isPost()) {
            //保存数据
            $ms_pnid = $Request->param('ms_pnid');
            $ms_blname = $Request->param('ms_blname');
            $ms_maname = $Request->param('ms_maname');
            $ms_baobian = $Request->param('ms_baobian');
            //查询是否已存在数据
            $byname = Db::name('material_set')->where('ms_pnid',$ms_pnid)
                ->where('ms_blname', $ms_blname)
                ->where('ms_maname', $ms_maname)
                ->where('ms_baobian', $ms_baobian)->find();
            if ($byname) {
                if ($pid != $byname['msid']) {
                    $this->error('数据库已存在原料设定');
                }
            }

            $lx = $Request->param('liaox/a');
            $name = $Request->param('name/a');
            if (!is_array($lx) && !is_array($name)) {
                $this->error('非法数据');
            }
            $ment = [];
            if (!empty($lx) && !empty($name)) {
                foreach ($lx as $klx=>$vlx) {
                    if (empty($name[$klx])) {
                        $this->error('对应数量不能为空');
                    }
                }
                foreach ($lx as $kl=>$vl) {
                    if ($vl == 'on') {
                        if(preg_match("/^[0-9]{1,2}+(.[5]{1})?$/",$name[$kl])){
                            $ment[] = $kl . ':' . $name[$kl];
                        } else {
                            $this->error('输入数值错误');
                        }
                    }
                }
            }

            if (empty($ment)) {
                $this->error('所关联的料型不能为空');
            } else {
                $MaterialSet = new MaterialSet();
                $MaterialSet->save([
                    'ms_pnid' => $ms_pnid,
                    'ms_blname' => $ms_blname,
                    'ms_maname' => $ms_maname,
                    'ms_baobian' => $ms_baobian,
                    'ms_gl' => join(',', $ment),
                ],['msid' => $pid]);
                $this->success('修改成功', Url::build('config/produce'));
            }

        } else {
            $data = MaterialSet::get($pid);
            //系列
            $number = Db::name('product_number')->field('pn_name')->select();
            //板材
            $bancai = Db::name('bancai_list')->field('blname')->select();
            //属性
            $attribute = Db::name('material_att')->select();
            //料型
            $liaox = Db::name('storage_charge')->field('lxid,lxxhao,lxname')->order('lxid asc')->select();
            //转换数组
            $blgl = explode(',', $data['ms_gl']);
            foreach ($blgl as $key=>$val) {
                $exp[] = explode(':', $val);
            }
            //
            foreach ($liaox as $kd=>$vd) {
                $liaox[$kd]['on']  = '';
                $liaox[$kd]['val'] = '';

                foreach ($exp as $ke=>$ve) {
                    if ($vd['lxid'] == $ve[0]) {
                        $liaox[$kd]['on']  = $ve[0];
                        $liaox[$kd]['val'] = $ve[1];
                    }
                }
            }
            //
            $assign = [
                'title' => '修改设定',
                'data' => $data,
                'number' => $number,
                'bancai' => $bancai,
                'attribute' => $attribute,
                'liaox' => $liaox,
            ];
            $this->assign($assign);
            return $this->fetch();
            //p($liaox);
        }
    }

    //删除板材
    public function bancai_delete() {
        // 是否有权限
        IS_ROOT(['1'])  ? true : $this->error('没有权限');

        $Request = Request::instance();
        if ($Request->isPost()) {
            $pid = $Request->param('pid');
            $name = $Request->param("name");
            if (empty($pid)) {
                $this->error('传入参数错误');
            }
            if ($name == 'delone') {
                // 单条删除操作
                if (!MaterialSet::get($pid)) {
                    $this->error('参数错误');
                }
                //删除板材
                MaterialSet::where('msid', $pid)->delete();

                $this->success('删除成功', Url::build('config/produce'));
            } else {
                $this->error('传入参数错误');
            }
        }
    }
}