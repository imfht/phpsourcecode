<?php
namespace plugins\webpage\admin;

use app\common\controller\AdminBase; 

class WebMenu extends AdminBase
{
	
	
	public function index()
    {
		
		
		
		if(IS_POST){
			  
			if ( 1 ) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
		}
		
		
		return $this->pfetch();
	}

}
