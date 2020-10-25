<?php
/**
 * 用户积分统计信息管理
 */
class user_scoresumAction extends backendAction
{

    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('user_scoresum');
    }
 protected function _search() {
        $map = array();
        if( $keyword = $this->_request('keyword', 'trim') ){
        	
            $map1['username'] = array('like', "%".$keyword."%");
            $idarr=D('user')->where($map1)->getField('uid',true);
            $map['uid']=array('in',$idarr);
        }
        
        
        
        
        
        
        $this->assign('search', array(
            'keyword' => $keyword,
        ));
       
        return $map;
    }

    public function _before_index() {
    	 $this->list_relation = true;
       
        
        
    }
   

}