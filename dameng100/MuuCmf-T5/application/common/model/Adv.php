<?php

namespace app\common\model;

use think\Model;

class Adv extends Model
{
    protected $tableName = 'adv';

    /*  展示数据  */
    public function getAdvList($name, $path)
    {

        $list = cache('adv_list_' . $name . $path);
        if ($list === false) {
            $now_theme = modC('NOW_THEME', 'default', 'Theme');

            $advPos = model('common/AdvPos')->getInfo($name, $path); //找到当前调用的广告位
            if ($advPos['theme'] != 'all' && !in_array($now_theme, explode(',', $advPos['theme']))) {
                return null;
            }

            $advMap['pos_id'] = $advPos['id'];
            $advMap['status'] = 1;
            $advMap['start_time'] = array('lt', time());
            $advMap['end_time'] = array('gt', time());
            $list = $this->where($advMap)->order('sort asc')->select();
            $list = collection($list)->toArray();

            foreach ($list as &$v) {
                $d = json_decode($v['data'], true);
                if (!empty($d)) {
                    $v = array_merge($d, $v);

                }
            }
            unset($v);
            cache('adv_list_' . $name . $path, $list);
        }

        return $list;
    }

    /**
    *设置为删除状态
    **/
    public function setDel($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $map['id']=array('in',$ids);
        $res=$this->where($map)->setField('status',-1);
        return $res;
    }
    /**
    *真实删除内容
    **/
    public function setTrueDel($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $map['id']=array('in',$ids);
        $res=$this->where($map)->delete();
        return $res;
    }
}