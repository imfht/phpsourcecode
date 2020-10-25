<?php
namespace app\muushop\widget;

use think\Controller;

/**
 * 展示位widget
 * 用于动态调用商品信息
 */

class Position extends Controller{
	
	/**
     * [lists description]
     * @param  integer $pos      定位ID
     * @param  [type]  $category 分类ID
     * @return [type]            [description]
     */
	public function lists($pos = 1,$cat_id = null){

        $title = modC('MUUSHOP_POS_'.$pos.'_TITLE','','Muushop');
        $description = modC('MUUSHOP_POS_'.$pos.'DESCRIPTION','','Muushop');

        $num = modC('MUUSHOP_POS_'.$pos.'COUNT', 8, 'Muushop');
        $field = modC('MUUSHOP_POS_'.$pos.'ORDER_FIELD', 'click_cnt', 'Muushop');
        $order = modC('MUUSHOP_POS_'.$pos.'ORDER_TYPE', 'desc', 'Muushop');
        $cache = modC('MUUSHOP_POS_'.$pos.'CACHE_TIME', 600, 'Muushop');
        $lists = cache('product_pos_'.$pos.'_data');
        if(empty($list)){
            /**
             * 获取推荐位数据列表
             * @param  number  $pos      推荐位
             * @param  number  $category 分类ID
             * @param  number  $limit    列表行数
             * @param  boolean $filed    查询字段
             * 
             * @return array             数据列表
             */
            $lists=model('muushop/MuushopProduct')->position($pos,$cat_id,$num,$field . ' ' . $order);
            cache('product_pos_'.$pos.'_data',$lists,$cache);
        }
        
        $this->assign('title', $title);
        $this->assign('descriptin', $description);      
		$this->assign('lists', $lists);
		return $this->fetch('widget/position');
	}
}
