<?php
class doc_conAction extends backendAction
{
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('doc_con');
        $this->_cate_mod = D('doc_cate');
        
    }

    public function _before_index() {
        $res = $this->_cate_mod->field('id,name')->select();
        $cate_list = array();
        foreach ($res as $val) {
            $cate_list[$val['id']] = $val['name'];
        }
        $this->assign('cate_list', $cate_list);

        // 取专辑列表
        $mod=D('zj');
        $where['status']  = '1';
        $zjlist=$mod->where($where)->select();
        foreach ($zjlist as $val) {
            $zj_list[$val['id']] = $val['title'];
        }
        $this->assign('zjlist',$zj_list);

        $p = $this->_get('p','intval',1);
        $this->assign('p',$p);

        $catelist = $this->_cate_mod->where(array('pid'=>0,'status'=>1))->select();
        foreach ($catelist as $k => $v) {
             $catelist[$k]['erji'] = $this->_cate_mod->where(array('pid'=>$v['id'],'status'=>1))->select();
        }
        $this->assign('catelist', $catelist);

       
    }
    public function _before_edit() {

        $catelist = $this->_cate_mod->where(array('pid'=>0,'status'=>1))->select();
        foreach ($catelist as $k => $v) {
             $catelist[$k]['erji'] = $this->_cate_mod->where(array('pid'=>$v['id'],'status'=>1))->select();
        }
        $this->assign('catelist', $catelist);

        // 取专辑列表
        $mod=D('zj');
        $where['status']  = '1';
        $zjlist=$mod->where($where)->select();
        $this->assign('zjlist',$zjlist);
        
        $id=$this->_request('id','intval');
        $cateid=$this->_mod->where(array('id'=>$id))->getField('cateid');
        $pid=$this->_cate_mod->where(array('id'=>$cateid))->getField('pid');
        if($pid){
        
        $cateid=$pid.'|'.$cateid;
        
        }else{
        
        $cateid=$pid;
        }
        
        
       $this->assign('cateid',$cateid);
    }
public function _before_delete($ids){
    
    
    
    
    
    
    $map['id']=array('in',$ids);
    
    
    $uidarr=D('doc_con')->where($map)->getField('uid',true);
    
    
    $score=C('wkcms_score_rule.docdel');
    
    
    opuserscore($uidarr,0,'score',$score);
    opuserscore($uidarr,0,'chengfa',$score);
    
    
}

public function _after_delete($ids){
    
     $this->delete_doc_file($ids);
    
}
    public function delete_doc_file($ids){
     
        //删除会员的同时删除图像
    $idarr=explode(',',$ids);
   
   
    $paths =C('wkcms_attach_path');
    
    
     foreach ($idarr as $id) {
     $map['id']=$id;
     $file=M('doc_con')->where($map)->find();
     
     
        @unlink($paths.'doc_con/'. $file['fileurl']);
      if(C('wkcms_web_model')==3){
        
        @unlink($paths.'docswf/'. $file['filename'].'.swf');
        @unlink($paths.'docswf/preview/'. $file['filename'].'.jpg');
        
      }
         
        
        }
    }

    protected function _search() {
        $map = array();
        ($time_start = $this->_request('time_start', 'trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = $this->_request('time_end', 'trim')) && $map['add_time'][] = array('elt', strtotime($time_end)+(24*60*60-1));
        ($status = $this->_request('status', 'trim')) && $map['status'] = $status;
        ($keyword = $this->_request('keyword', 'trim')) && $map['title'] = array('like', '%'.$keyword.'%');
        $cate_id = $this->_request('cate_id', 'intval');
        $selected_ids = '';
        if ($cate_id) {
            $id_arr = $this->_cate_mod->where(array('pid'=>$cate_id))->getField('id',true);
            $id_arr[]=$cate_id;
            $map['cateid'] = array('IN', $id_arr);
            $pid=$this->_cate_mod->where(array('id'=>$cate_id))->getField('pid');
         if($pid){
        
        $selected_ids=$pid.'|'.$cate_id;
        
        }else{
        
        $selected_ids=$pid;
        }
         
           
        }
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            'cate_id' => $cate_id,
            'selected_ids' => $selected_ids,
            'status'  => $status,
            'keyword' => $keyword,
        ));
        return $map;
    }


}