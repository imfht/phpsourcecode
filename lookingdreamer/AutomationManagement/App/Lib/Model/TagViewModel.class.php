<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

import('ViewModel');
// 标签视图模型
class TagViewModel extends ViewModel {
    protected $viewFields =  array(
        'Tag'=>array('name','module'),
        'Tagged'=>array('user_id','tag_id','record_id'),
        'Topic'=>array('title','id','create_time','read_count','comment_count','status')
        );

    protected $viewCondition = array(
        'Tagged.tag_id'    =>array('eqf','Tag.id'),
        'Topic.id'=>array('eqf','Tagged.record_id'),
        'Tagged.module'=>'Topic',

        );
}
?>