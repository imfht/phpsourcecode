<?php

namespace Common\Model;

use Think\Model;

/** 
 * @author Lain
 * 
 */
class PosterModel extends Model {

    public function getDetailById($id){
        $map['id'] = $id;
        $detail = $this->where($map)->find();
        $detail['setting'] = string2array($detail['setting']);
        return $detail;
    }

    public function update($id, $info){
        $info['setting'] = array2string($this->check_setting($info['setting']));
        return $this->where(['id' => $id])->save($info);
    }

    public function create($info){
        $info['setting'] = array2string($this->check_setting($info['setting']));
        return $this->add($info);
    }

    public function deleteItem($id){
        $map['id'] = $id;
        $detail = $this->where($map)->delete();
    }
    public function check_setting($setting){
        if(is_array($setting['images'])) {
            foreach ($setting['images'] as $k => $s) {
                if($s['linkurl']=='http://') {
                    $setting['images'][$k]['linkurl'] = '';
                }
                if (!$s['imageurl']) unset($setting['images'][$k]);
            }
        }
        return $setting['images'];
    }
}

?>