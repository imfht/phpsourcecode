<?php

namespace Common\Model;

use Think\Model;

/**
 * @author Lain
 *
 */
class PositionModel extends Model
{

    public function getAllPosition()
    {
        $list = $this->index('posid')->select();
        return $list;
    }

    public function positionUpdate($id, $modelid, $catid, $posid, $data, $expiration = 0, $undel = 0, $model = 'content_model')
    {

        //删除旧的
        $map['id']      = $id;
        $map['modelid'] = $modelid;
        $map['catid']   = $catid;
        M('position_data')->where($map)->delete();
        if (is_array($posid) && !empty($posid)) {
            foreach ($posid as $pid) {
                $info          = [];
                $info          = $map;
                $info['posid'] = $pid;
                $info['data']  = array2string($data);
                $info['thumb'] = empty($data['thumb']) ? 0 : 1;
                $result        = M('position_data')->add($info);
            }
        }
    }

    /**
     * 推荐位修改
     * @Author   pdosgk
     * @DateTime 2019-04-17
     * @param    [type]     $id      [description]
     * @param    [type]     $modelid [description]
     * @param    [type]     $posid   [description]
     * @param    [type]     $info    [description]
     * @return   [type]              [description]
     */
    public function itemEdit($id, $modelid, $posid, $info, $synedit = 0){
        $detail = $this->getPosition($posid, $modelid, $id);
        $data = string2array($detail['data']);
        $new_data = array_merge($data, $info);

        $map['posid'] = $posid;
        $map['modelid'] = $modelid;
        $map['id'] = $id;

        $update_info['data'] = array2string($new_data);
        $update_info['synedit'] = $synedit;

        M('position_data')->where($map)->save($update_info);
        return true;
    }

    //获取推荐文章
    public function getList($posid, $num = 0, $params = [])
    {
        if (isset($params['catid']) && $params['catid']) {
            $map['catid'] = $params['catid'];
        }
        $map['posid'] = $posid;
        $pos_arr      = M('position_data')->where($map)->limit($num)->select();
        if (empty($pos_arr)) {
            return [];
        }
        $array = [];
        foreach ($pos_arr as $info) {
            $key         = $info['catid'] . '-' . $info['id'];
            $array[$key] = array_merge(string2array($info['data']), $info);
        }
        return $array;
    }

    public function getPosition($posid, $modelid, $id){
        $map['posid'] = $posid;
        $map['modelid'] = $modelid;
        $map['id'] = $id;
        return M('position_data')->where($map)->find();
    }
}
