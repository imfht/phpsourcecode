<?php
/**
 * 用户信息管理
 */
class userAction extends backendAction
{

    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('user');
    }


    //重写列表页，带搜索 - wfgo
    public function index() {

        $user = M('user');
        $map = array();
        $keyword = $this->_request('keyword', 'trim');
        $keyword && $map = array('username'=>array('like', '%'.$keyword.'%'));
        $count = $user->where($map)->count();
        $pager = new Page($count, 15);
        $list = $user->order('uid DESC')->where($map)->limit($pager->firstRow.','.$pager->listRows)->select();
        foreach ($list as $k => $v) {
           $shenfen =  M('user_role')->where(array('id'=>$v['roleid']))->find();
           $list[$k]['shenfen'] =  $shenfen['name'];
        }

        $this->assign('list',$list);
        $this->assign('page',$pager->show());
        $this->assign('keyword', array('keyword' => $keyword,));
        $this->display();
    }



    protected function _search() {
        $map = array();
        if( $keyword = $this->_request('keyword', 'trim') ){
        	
            $map['_string'] = "username like '%".$keyword."%";
        }
        $this->assign('search', array(
            'keyword' => $keyword,
        ));
        return $map;
    }

    


    public function _before_add() {
        //$pid = $this->_get('pid', 'intval', 0);
       $role= M('user_role')->where(array('status'=>1))->select();
            //dump($pid);
         $this->assign('role', $role);
       
    }
    public function _before_edit() {
        $role= M('user_role')->where(array('status'=>1))->select();
            //dump($pid);
         $this->assign('role', $role);
    }
    public function _before_insert($data) {
        if( ($data['password']!='')&&(trim($data['password'])!='') ){
            $data['password'] = $data['password'];
        }else{
            unset($data['password']);
        }

        return $data;
    }

    public function _after_insert($id) {
        $img = $this->_post('img','trim');
        $this->user_thumb($id,$img);
    }

    public function _before_update($data) {
        if( ($data['password']!='')&&(trim($data['password'])!='') ){
        	
        	
            $data['password'] = md5($data['password'].C('web_md5'));
        }else{
            unset($data['password']);
        }
    
        return $data;
    }

    public function _after_update($id){
        $img = $this->_post('img','trim');
        if($img){
            $this->user_thumb($id,$img);
        }
        
    }
public function _after_delete($ids){
	M('userinfo')->delete($ids);
    M('user_scoresum')->delete($ids);
     $this->delete_user_thumb($ids);
          
       

}
    public function user_thumb($id,$img){
        $img_path= avatar_dir($id);
        //会员头像规格
        $avatar_size = explode(',', C('wkcms_avatar_size'));
        $paths =C('wkcms_attach_path');

        foreach ($avatar_size as $size) {
            if($paths.'avatar/'.$img_path.'/' . md5($id).'_'.$size.'.jpg'){
                @unlink($paths.'avatar/'.$img_path.'/' . md5($id).'_'.$size.'.jpg');
            }
            !is_dir($paths.'avatar/'.$img_path) && mkdir($paths.'avatar/'.$img_path, 0777, true);
            Image::thumb($paths.'avatar/temp/'.$img, $paths.'avatar/'.$img_path.'/' . md5($id).'_'.$size.'.jpg', '', $size, $size, true);
        }

        @unlink($paths.'avatar/temp/'.$img);
    }
    public function delete_user_thumb($ids){
        $obj_dir = new Dir;
            //删除会员的同时删除图像
        $idarr=explode(',',$ids);
        $avatar_size = explode(',', C('wkcms_avatar_size'));
        $paths =C('wkcms_attach_path');
        foreach ($idarr as $id) {

            $img_path= avatar_dir($id);
            $dir = $paths.'avatar/'.$img_path;
            if (is_dir($dir)) {
                $obj_dir->delDir($dir);
            }
        }
           
    }
    

    public function ajax_upload_imgs() {
        //上传图片
         
        if (!empty($_FILES['file']['name'])) {
            $result = $this->_upload($_FILES['file'], 'avatar/temp/' );
            if ($result['error']) {
            	$data['status']=0;
            	$data['info']=$result['info'];
                //$this->error($result['info']);
            }else {
                $data['info'] =  $result['info'][0]['savename'];
                
                $data['status']=1;
                
               // $this->ajaxReturn(1, L('operation_success'), $data['img']);
            }


        } else {
        	$data['status']=0;
        	$data['info']='无文件上传';
            //$this->ajaxReturn(0, L('illegal_parameters'));
        }
        echo json_encode($data);
    }

    /**
     * ajax检测会员是否存在
     */
    public function ajax_check_name() {
        $name = $this->_get('username', 'trim');
        $id = $this->_get('id', 'intval');
        
        if ($this->_mod->name_exists($name,  $id)) {
            $this->ajaxReturn(0, '该会员已经存在');
        } else {
            $this->ajaxReturn();
        }
    }

    /**
     * ajax检测邮箱是否存在
     */
    public function ajax_check_email() {
        $name = $this->_POST('email', 'trim');
        $id = $this->_get('id', 'intval');
        if ($this->_mod->email_exists($name,  $id)) {
            $this->ajaxReturn(0, '该邮箱已经存在');
        } else {
            $this->ajaxReturn();
        }
    }

}