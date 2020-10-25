<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use app\admin\model\Alonepage as AlonepageModel;

class Alonepage extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [
			['text', 'title', '标题'],
	        ['text', 'keywords', 'SEO关键字'],
			['text', 'descrip', '分享描述'],
			['jcrop', 'picurl', '分享图片'],
	        ['text', 'template', '模板路径','路径要包含风格名,只能放在index_style目录下,比如:“qiboxx/index/alonepage/pc_index.htm”'],
		    ['radio', 'status', '是否启用', '', [1 => '启用', 0 => '禁用'], 1],
			['ueditor', 'content', '内容'],					
	];
	protected $list_items;
	protected $tab_ext = [
			'page_title'=>'单篇文章独立页管理',
	        'top_button'=>[
	                ['type'=>'add'],
	                ['type'=>'delete'],
	        ]
	];
	
	protected function _initialize()
    {
	    if ($this->request->isPost()) {
	        if(input('template')){
	            if (!is_file(TEMPLATE_PATH . 'index_style/' . input('template'))) {
	                $this->error('模板路径有误!');
	            }
	        }
	    }
		parent::_initialize();
		$this->model = new AlonepageModel();
		$this->list_items = [				 
				['title', '单独页名称', 'link',iurl('index/alonepage/index',['id'=>'__id__']),'_target'],                
				['posttime', '发布时间', 'datetime'],
		        ['status', '是否启用', 'switch'],	
		];
	}
}
