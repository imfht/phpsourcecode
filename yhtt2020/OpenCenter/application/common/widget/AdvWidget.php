<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: lin(lt@ourstu.com)
 * Date: 2018/9/11
 * Time: 13:24
 * ----------------------------------------------------------------------
 */

namespace app\common\widget;

use app\common\model\Adv;
use app\common\model\AdvPos;
use think\Controller;

class AdvWidget extends Controller
{
    /**
     * @param $param
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author:lin(lt@ourstu.com)
     */
    public function render($param)
    {

        if (is_array($param)) {
            $name = $param['name'];
        } else {
            $name = $param;
            $param = array();
        }
        $advPosModel = new AdvPos;
        $path = request()->module() . '/' . request()->controller() . '/' . request()->action();

        $pos = $advPosModel->getInfo($name, $path);

        //不存在广告位则创建
        if (empty($pos)) {
            empty($param['type']) && $param['type'] = 3;
            empty($param['status']) && $param['status'] = 1;
            empty($param['width']) && $param['width'] = '100px';
            empty($param['height']) && $param['height'] = '100px';
            empty($param['theme']) && $param['theme'] = 'all';
            empty($param['title']) && $param['title'] = $name;
            empty($param['margin']) && $param['margin'] = '';
            empty($param['padding']) && $param['padding'] = '';
            empty($param['data']) && $param['data'] = array();
            $param['name'] = $name;
            $param['path'] = $path;
            $param['data'] = json_encode($param['data']);
            $pos = $advPosModel::create($param);
            cache('adv_pos_by_pos_' . $path . $name, $pos);
        }
        $data = json_decode($pos['data'], true);
        if (!empty($data)) {
            $pos = array_merge($pos, $data);
        }
        $advModel = new Adv;
        $list = $advModel->getAdvList($name, $path);
        $this->assign('list', $list);
        $this->assign('pos', $pos);
        if (empty($list)) {
            $tpl = 'empty';
        } else {
            switch ($pos['type']) {
                case 1:
                    $tpl = 'single_pic';
                    break;
                case 2:
                    $tpl = 'slider';
                    break;
                case 3:
                    $tpl = 'text';
                    break;
                case 4:
                    $tpl = 'code';
                    break;
                default:
                    $tpl = 'empty';
            }
        }
        return $this->fetch('common@widget/adv_' . $tpl);
    }
}