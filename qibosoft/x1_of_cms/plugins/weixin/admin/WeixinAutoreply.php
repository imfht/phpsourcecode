<?php
namespace plugins\weixin\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;
use app\common\model\Config AS ConfigModel;

use plugins\weixin\model\WeixinAutoreply as WeixinAutoreplyModel;

class WeixinAutoreply extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [				
				'page_title'=>'微信关键字自动回复',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new WeixinAutoreplyModel();
		$this->list_items = [				 
				['ask', '关键字', 'text'],                
				['type', '回复类型', 'select',[0=>'纯文字',1=>'图文']],
				['answer', '回复内容', 'callback',function($value,$data){
						return $this->model->format_list_show($data['type'],$value);
					},
				'__data__'],
			];
	}
	
	public function add(){		
		if(IS_POST){
			$data = get_post('post');
			$data['answer'] = $this->model->format_answer($data['type'],$data['answers'],$data['answer']);
			if(!empty($this->validate)){
				// 验证
				$result = $this->validate($data, $this->validate);
				if(true !== $result) $this->error($result);				
			}
			$_data = [
				'type'=>$data['type'],
				'ask'=>$data['ask'],
				'answer'=>$data['answer'],
			];
            if ($type = $this->model->create($_data)) {
                $this->success('添加成功', 'index');
            } else {
                $this->error('添加失败');
            }
		}
		return $this->pfetch();
	}
	
	public function edit($id=0){
		
	    if (empty($id)) $this->error('缺少参数');
		
		// 保存数据
        if (IS_POST) {

            // 表单数据
            $data = get_post('post');
			$data['answer'] = $this->model->format_answer($data['type'],$data['answers'],$data['answer']);

            // 验证
			if(!empty($this->validate)){
				// 验证
				$result = $this->validate($data, $this->validate);
				if(true !== $result) $this->error($result);				
			}
			$_data = [
				'id'=>$id,
				'type'=>$data['type'],
				'ask'=>$data['ask'],
				'answer'=>$data['answer'],
			];
            if ($this->model->update($_data)) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
		
		$info = $this->model->get($id);

		
		return $this->pfetch('edit',['rsdb'=>$info,'id'=>$id]);
	}

	public function firstreply()
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
