<?php

namespace app\admin\controller;

use think\Loader;
use think\Config;
use think\Db;
use think\Url;

/**
 * Description of Action
 * 用户行为管理
 * @author static7
 */
class Action extends Admin {

    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function index() {
        //获取列表数据
        $data = Loader::model('Action')->actionList([], true, 'id DESC');
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
        ];
        $this->view->metaTitle = '行为列表';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 新增或者编辑行为
     * @param int $id 行为ID
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */

    public function edit($id = 0) {
        if ((int) $id > 0) {
            $data = Loader::model('Action')->edit($id);
            $value['info'] = $data ?? null;
        }
        $this->view->metaTitle = (int) $id > 0 ? '编辑' : '新增' . '行为';
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 用户更新或者添加行为
     * @author staitc7 <static7@qq.com>
     */

    public function renew() {
        $info = Loader::model('Action')->renew();
        return is_array($info) ?
                $this->success('操作成功', Url::build('index')) :
                $this->error($info);
    }

    /**
     * 行为日志
     * @author staitc7 <static7@qq.com>
     */

    public function actionLog() {
        $list = Db::name('ActionLog')
                ->where('status', 'neq', -1)
                ->order('create_time DESC')
                ->paginate(Config::get('list_rows') ?? 10);
        $value = [
            'list' => $list,
            'page' => $list->render() ?? null,
        ];
        $this->view->metaTitle = '行为日志';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 行为日志详细
     * @param int $id 日志ID
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */

    public function detailed($id = 0) {
        (int) $id || $this->error('参数错误');
        $info = Db::name('ActionLog')->where('status', 'neq', -1)->find($id);
        empty($info) && $this->error('该条日志不存在');
        $this->view->metaTitle = '行为日志详情';
        return $this->view->assign(['info' => $info])->fetch();
    }

    /**
     * 删除日志
     * @param mixed $ids
     * @author huajie <banhuajie@163.com>
     */
    public function remove($ids = 0) {
        (int) $ids || $this->error('参数错误！');
        if (is_array($ids)) {
            $map['id'] = ['in', $ids];
        } elseif (is_numeric((int) $ids)) {
            $map['id'] = $ids;
        }
        $res = Db::name('ActionLog')->where($map)->delete();
        return $res !== false ?
                $this->success('删除成功') :
                $this->error('删除失败');
    }

    /**
     * 清空日志
     */
    public function clear() {
        $res = Db::name('ActionLog')->where('1=1')->delete();
        return $res !== false ?
                $this->success('日志清空成功！') :
                $this->error('日志清空失败！');
    }

}
