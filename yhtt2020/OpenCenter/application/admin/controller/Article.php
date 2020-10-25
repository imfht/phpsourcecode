<?php
/**
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: sun(slf02@ourstu.com)
 * Date: 2018/9/25
 * Time: 14:16
 */

namespace app\admin\controller;

use think\Controller;
use app\admin\model\AdminLog;

class Article extends Controller
{
    /**
     * @return mixed|\think\response\Json
     * @author sun slf02@ourstu.com
     * @date 2018/9/26 14:35
     * //文章列表页
     */
    public function index()
    {
        $page = input('get.page', 1, 'intval');
        $limit = input('get.limit', 10, 'intval');
        if ($this->request->isAjax()) {
            $map[] = ['status', 'in', '0,1'];
            $list = db('Article')->where($map)->page($page, $limit)->order('create_time desc')->select();
            foreach ($list as &$val) {
                $val['create_time'] = time_format($val['create_time']);
                $val['update_time'] = time_format($val['update_time']);
                $val['cover'] = pic($val['cover']);
            }
            unset($val);
            $count = db('Article')->where($map)->count();
            $data = [
                'code' => 0,
                'message' => '数据返回成功',
                'count' => $count,
                'data' => $list
            ];
            AdminLog::setTitle('获取文章列表');
            return json($data);
        }
        AdminLog::setTitle('文章列表');
        return $this->fetch();
    }

    public function editArticle()
    {
        if ($this->request->isPost()) {
            $info['id'] = input('post.id', 0, 'intval');
            $info['cover'] = input('post.cover', 0, 'intval');
            $info['status'] = input('post.status', 0, 'intval');
            $info['content'] = input('post.content');
            $info['title'] = input('post.title');
            if ($info['id']) {
                $info['update_time'] = time();
                $res = db('Article')->save($info, ['id' => $info['id']]);
            } else {
                $info['create_time'] = $info['update_time'] = time();
                $info['view'] = 0;
                $res = db('Article')->insert($info);
            }
            if ($res) {
                $this->error($info['id'] ? '编辑' : '新增' . '成功');
            } else {
                AdminLog::setTitle('编辑文章列表');
                $this->success($info['id'] ? '编辑' : '新增' . '失败');
            }
        }
        $id = input('get.id', 0, 'intval');
        $data = db('Article')->find($id);
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author sun slf02@ourstu.com
     * @date 2018/9/26 16:24
     * 删除记录
     */
    public function setStatus()
    {
        $id = input('post.id');
        $status = input('status', 0, 'intval');
        if (!$id) {
            $this->error('删除文章id不能为空');
        }
        $res = db('Article')->where('id', 'in', $id)->update(['status' => $status]);
        if ($res) {
            AdminLog::setTitle('删除文章');
            $this->success('操作成功');
        }
    }
}