<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase;
use app\common\model\Config AS ConfigModel;

class Operation extends AdminBase
{
	public function set()
    {
		if(IS_POST){
			$data = get_post('post');
			$model = new ConfigModel();
			if ( $model->save_data($data['webdbs']) ) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
		}
		
		return $this->pfetch();
	}
}
