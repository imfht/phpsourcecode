<?php

namespace app\common\widget;

use think\Controller;
use think\Request;


class Adv extends Controller
{

    public function render($param)
    {
        if (is_array($param)) {
            $name = $param['name'];
        } else {
            $name = $param;
            $param = array();
        }
        $advPosModel = model('common/AdvPos');
        $request = Request::instance();
        $path = $request->module() . '/' . $request->controller() . '/' . $request->action();
        $pos = $advPosModel->getInfo($name, $path);
        
        //不存在广告位则创建
        if (empty($pos)) {
            empty($param['type']) && $param['type'] = 0;
            empty($param['status']) && $param['status'] = 1;
            empty($param['width']) && $param['width'] = '100px';
            empty($param['height']) && $param['height'] = '100px';
            empty($param['theme']) && $param['theme'] = 'all';
            empty($param['title']) && $param['title'] = $name;
            empty($param['margin']) && $param['margin'] = '';
            empty($param['padding']) && $param['padding'] = '';
            empty($param['data']) && $param['data'] = [];
            $param['name'] = $name;
            $param['path'] = $path;
            $param['data']=json_encode($param['data']);
            $advPos = $advPosModel->save($param);
            $pos['id'] = $advPos->id;
            cache('adv_pos_by_pos_' . $path . $name, $pos);
        }
        
        $pos['type_text'] = $advPosModel->switchType($pos['type']);
        $data = json_decode($pos['data'], true);
        if (!empty($data)) {
            $pos = array_merge($pos, $data);
        }
        $list = model('common/Adv')->getAdvList($name, $path);
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