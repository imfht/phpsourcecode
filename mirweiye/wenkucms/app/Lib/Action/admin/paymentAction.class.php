<?php
class paymentAction extends backendAction {
 public function _initialize() {
        parent::_initialize();
       $this->_mod = D('payment');
    }
	
	
	public function _before_index(){
  		
  	}
    protected function _before_update($data = '') {
        
    	$map['id']=$data['id'];
        $old_img = $this->_mod->where($map)->getField('logo');
        
        $paths =C('wkcms_attach_path');
        
        if($data['logo']!=$old_img){
        @unlink($paths.'payment/'.$old_img);
       
    	}
              return $data;
    }
   public function ajax_upload_img() {
        //上传图片
        if (!empty($_FILES['file']['name'])) {
            $result = $this->_upload($_FILES['file'], 'payment');
            if ($result['error']) {
            	$data['status']=0;
        	    $data['info']=$result['info'];
            	
               
            } else {
                
                $data['info'] = $result['info'][0]['savename'];
                $data['status']=1;
        	   
               
            }
        } else {
        	$data['status']=0;
        	$data['info']=L('illegal_parameters');
           
        }
        
        
        
        echo json_encode($data);
    }
}
?>