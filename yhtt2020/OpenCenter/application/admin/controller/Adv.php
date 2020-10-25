<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: lin(lt@ourstu.com)
 * Date: 2018/9/13
 * Time: 14:23
 * ----------------------------------------------------------------------
 */

namespace app\admin\controller;

use think\Controller;
use app\admin\model\AdminLog;

class Adv extends Controller
{
    protected $advPos;
    protected $adv;

    public function initialize()
    {
        $this->advPos = model('AdvPos');
        $this->adv = model('Adv');
    }

    /**
     * 广告位管理
     * @return mixed|\think\response\Json
     * @author:lin(lt@ourstu.com)
     */
    public function pos()
    {
        $page = input('get.page', 1, 'intval');
        $limit = input('get.limit', 10, 'intval');
        if ($this->request->isAjax()) {
            $map = ['status' => 1];
            $advList = $this->advPos
                ->where($map)
                ->page($page, $limit)
                ->order('id desc')
                ->select();
            foreach ($advList as &$val) {
                $val['type_text'] = $val->type_text;
                $val['status_text'] = $val->status_text;
            }
            unset($val);
            $count = $this->advPos->where($map)->count();
            $data = [
                'code' => 0,
                'message' => '数据返回成功',
                'count' => $count,
                'data' => $advList
            ];
            AdminLog::setTitle('获取广告位列表');
            return json($data);
        }
        AdminLog::setTitle('广告位列表');
        return $this->fetch();
    }

    /**
     * 编辑广告位
     * @return mixed
     * @author:lin(lt@ourstu.com)
     */
    public function editPos()
    {
        $advPosModel = $this->advPos;
        if ($this->request->isAjax()) {
            $pos['id'] = input('post.id', 0, 'intval');
            $pos['name'] = input('post.name', '', 'text');
            $pos['title'] = input('post.title', '', 'text');
            $pos['path'] = input('post.path', '', 'text');
            $pos['type'] = input('post.type', 1, 'intval');
            $pos['status'] = input('post.status', 1, 'intval');
            $pos['width'] = input('post.width', '', 'text');
            $pos['height'] = input('post.height', '', 'text');
            $pos['margin'] = input('post.margin', '', 'text');
            $pos['padding'] = input('post.padding', '', 'text');
            $pos['theme'] = input('post.theme', 'all', 'text');
            switch ($pos['type']) {
                case 2:
                    $pos['data'] = json_encode(array('style' => input('style', 1, 'intval')));
            }
            if ($pos['id'] == 0) {
                $result = $advPosModel::create($pos);
            } else {
                $result = $advPosModel->save($pos, ['id' => $pos['id']]);
            }

            if ($result === false) {
                $this->error('保存失败。');
            } else {
                cache('adv_pos_by_pos_' . $pos['path'] . $pos['name'], null);
                AdminLog::setTitle('编辑广告位成功');
                $this->success('保存成功。');
            }

        }
        $aId = input('get.id', 0, 'intval');
        $adv = $advPosModel::get($aId);
        $this->assign('adv', $adv);
        AdminLog::setTitle('编辑广告位');
        return $this->fetch();
    }

    /**
     * 删除广告位
     * @author:lin(lt@ourstu.com)
     */
    public function delPos()
    {
        $aId = input('post.id', '', 'strval');
        $advPosModel = $this->advPos;
        $advPosModel->where('id', 'in', $aId)->update(['status' => -1]);
        $pos = $advPosModel->where('id', 'in', $aId)->select();
        foreach ($pos as $val) {
            cache('adv_pos_by_pos_' . $val['path'] . $val['name'], null);
        }
        unset($val);
        AdminLog::setTitle('删除广告位');
    }

    /**
     * 新增广告
     * @return mixed
     * @author:lin(lt@ourstu.com)
     */
    public function appendAdv()
    {
        $advModel = $this->adv;
        $advPosModel = $this->advPos;
        $aId = input('id', 0, 'intval');
        $posId = input('pos_id', 0, 'intval');
        $advInfo = null;
        if ($aId != 0) {
            $advInfo = $advModel::get($aId);
            $advInfo['data'] = json_decode($advInfo['data'], true);
            $advInfo['create_time'] = date('Y-m-d', $advInfo['create_time']);
            $advInfo['start_time'] = date('Y-m-d', $advInfo['start_time']);
            $advInfo['end_time'] = date('Y-m-d', $advInfo['end_time']);
            $posId = $advInfo['pos_id'];
        }
        $advPos = $advPosModel::get($posId);
        if ($this->request->isAjax()) {
            $adv['id'] = input('post.id', 0, 'intval');
            $adv['title'] = input('post.title', '', 'text');
            $adv['pos_id'] = input('post.pos_id', 0, 'intval');
            $adv['data'] = input('post.data', '', 'text');
            $adv['url'] = input('post.url', '', 'text');
            $adv['sort'] = input('post.sort', 1, 'intval');
            $adv['status'] = input('post.status', 1, 'intval');
            $adv['create_time'] = strtotime(input('post.create_time', '', 'text'));
            $adv['start_time'] = strtotime(input('post.start_time', '', 'text'));
            $endTime = input('post.end_time', '', 'text');
            $adv['end_time'] = strtotime($endTime . " 23:59:59");
            $adv['padding'] = input('post.padding', '', 'text');
            $adv['target'] = input('post.target', '_blank', 'text');
            $data['target'] = $adv['target'];
            switch ($advPos['type']) {
                case 1:  //单图
                    $data['pic'] = input('pic', 0, 'intval');
                    break;
                case 2:  //多图
                    $data['pic'] = input('pic', 0, 'intval');
                    break;
                case 3:  //文字链接
                    $data['text'] = input('post.text', '', 'text');
                    $data['text_color'] = input('post.text_color', '', 'text');
                    $data['text_font_size'] = input('post.text_font_size', '', 'text');
                    break;
                case 4:  //代码
                    $data['code'] = input('code', '', 'html');
                    break;
            }
            $adv['data'] = json_encode($data);
            if ($adv['id'] == 0) {
                $result = $advModel::create($adv);
            } else {
                $result = $advModel->save($adv, ['id' => $adv['id']]);
            }

            if ($result === false) {
                $this->error('保存失败。');
            } else {
                cache('adv_list_' . $advPos['path'] . $advPos['name'], null);
                AdminLog::setTitle('新增广告');
                $this->success('保存成功。');
            }

        }
        $this->assign('pos', $advPos);
        $this->assign('adv', $advInfo);
        return $this->fetch();
    }

    /**
     * 广告管理
     * @return mixed|\think\response\Json
     * @author:lin(lt@ourstu.com)
     */
    public function adv()
    {
        $page = input('get.page', 1, 'intval');
        $limit = input('get.limit', 10, 'intval');
        $map['status'] = 1;
        if ($this->request->isAjax()) {
            $list = $this->adv->where($map)->order('pos_id desc,sort desc')->page($page, $limit)->select();
            foreach ($list as &$val) {
                $val['create_time'] = date('Y-m-d', $val['create_time']);
                $val['start_time'] = date('Y-m-d', $val['start_time']);
                $val['end_time'] = date('Y-m-d', $val['end_time']);
                $val['status_text'] = $val->status_text;
            }
            unset($val);
            $count = $this->adv->where($map)->count();
            $data = [
                'code' => 0,
                'message' => '数据返回成功',
                'count' => $count,
                'data' => $list
            ];
            AdminLog::setTitle('广告管理');
            return json($data);
        }
    }

    /**
     * 删除广告
     * @author:lin(lt@ourstu.com)
     */
    public function delAdv()
    {
        $aId = input('post.id', 0, 'intval');
        $advModel = $this->adv;
        $adv = $advModel::get($aId);
        $adv->status = -1;
        $adv->save();
        AdminLog::setTitle('删除广告');
    }

    /**
     * 上传图片
     * @return \think\response\Json
     * @author:lin(lt@ourstu.com)
     */
    public function upload()
    {
        // 获取表单上传文件
        $file = request()->file('file');

        $info = $file->move('../public/uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            $id = model('Picture')->upload($info);
            $result = [
                'code' => 0,
                'msg' => '上传成功',
                'id' => $id
            ];
        } else {
            // 上传失败获取错误信息
            $result = [
                'code' => -1,
                'msg' => $file->getError()
            ];
        }
        AdminLog::setTitle('上传图片');
        return json($result);
    }

}