<?php

namespace Common\Model;

use Think\Model;

/** 
 * @author Lain
 * 
 */
class PageModel extends Model {

    public function getDetailByCatid($catid){
        $map['catid'] = $catid;
        $detail = $this->where($map)->find();
        $detail['content'] = html_entity_decode($detail['content']);
        return $detail;
    }

}

?>