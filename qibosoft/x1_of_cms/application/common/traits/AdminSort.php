<?php
namespace app\common\traits;

use util\Tree;

trait AdminSort
{
	use AddEditList;
	
	protected function saveAddContent($data=[])
	{
	    // 保存数据
	    if ($this->request->isPost()) {
	        // 表单数据
	        $data || $data = $this->request->post();

	        if(!empty($this->validate)){
	            // 验证
	            $result = $this->validate($data, $this->validate);
	            if(true !== $result) $this->error($result);
	        }
	        
	        $array = [];
	        $detail = explode("\r\n",$data['name']);
	        foreach ($detail AS $value){
	            if (empty($value))continue;
	            //考虑到还有其它选项,比如uid
	            $array[] =array_merge($data,
    	                    [
    	                            'name'=>$value,
    	                            'uid'=>$this->user['uid'],
    	                            'pid'=>intval($data['pid']),
    	                    ]
	                    );
	        }
	        
	        
	        if ( $this->model->saveAll($array)) {
	            return true; 	//$result->id 方便其它地方通过这个得到新的ID
	        } else {
	            return false;
	        }
	    }
	}
	
	protected function getListData($map=[] , $order = 'list desc')
	{
        // 查询
	    $map = array_merge($this->getMap(),$map);

        // 数据列表
	    $data_list = $this->model->where($map)->order($order)->column(true);
        
        //树状重新排序处理
        if (!empty($data_list)) {
            $data_list = Tree::config(['title' => 'name'])->toList($data_list);
            
            foreach($data_list AS &$rs){
                $rs['name'] = $rs['title_display'];
            }
        }

		return $data_list;
	}
}