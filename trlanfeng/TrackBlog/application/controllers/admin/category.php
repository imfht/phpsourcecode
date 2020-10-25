<?php
/**
* 栏目管理
*/
class Category extends TB_Admin
{
	
	public function __construct()
	{
		parent::__construct();
		//加载model
		$this->load->model('category_model');
	}
	public function index()
	{
		$this->show_list();
	}
	public function show_one($id)
	{
		$data = $this->category_model->getOne($id);
		$this->load->view('admin/header');
		$this->load->view('admin/category_one',$data);
		$this->load->view('admin/footer');
	}

	public function show_list()
	{
		$filter = array();
		$data['articleList'] = $this->category_model->getList($filter,15,0,"orders ASC");
		$this->load->view('admin/header');
		$this->load->view('admin/category_list',$data);
		$this->load->view('admin/footer');
	}
	public function add()
	{
		if (isset($_POST['submit'])) {
			if ($this->category_model->add($_POST)) {
				unset($_POST);
				$this->trackblog->showMessage('success', '栏目添加成功！', '/admin/category/show_list');
			} else {
				$this->trackblog->showMessage('danger', '栏目添加失败，未知错误！如需帮助，请联系开发者。微博：@孤月蓝风', '/admin/category/show_list');
			}
		} else {
			$filter = array();
			$catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
			$data['catlist'] = $catList;
			$data['name'] = "";
			$data['nickname'] = "";
			$data['fid'] = 0;
			$data['intro'] = "";
			$data['orders'] = "";
			$data['status'] = 1;
			$data['keywords'] = "";
			$data['description'] = "";
			$data['controlType'] = "add";
			$this->load->view('admin/header');
			$this->load->view('admin/category_one',$data);
			$this->load->view('admin/footer');
		}
	}
	public function edit($id)
	{
		if (isset($_POST['submit'])) {
			if ($this->category_model->edit($id, $_POST)) {
				unset($_POST);
				$this->trackblog->showMessage('success', '栏目修改成功！', '/admin/category/show_list');
			} else {
				$this->trackblog->showMessage('danger', '栏目修改失败，未知错误！如需帮助，请联系开发者。微博：@孤月蓝风', '/admin/category/show_list');
			}
		} else {
			$data = $this->category_model->getOne($id);
			$filter = array();
			$catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
			$data['catlist'] = $catList;
			$data['controlType'] = "edit";
			$this->load->view('admin/header');
			$this->load->view('admin/category_one', $data);
			$this->load->view('admin/footer');
		}
	}
	public function delete($id)
	{
		if ($this->category_model->delete($id)) {
			$this->trackblog->showMessage('success', '栏目删除成功！', '/admin/category/show_list');
		} else {
			$this->trackblog->showMessage('danger', '栏目删除失败，未知错误！如需帮助，请联系开发者。微博：@孤月蓝风', '/admin/category/show_list');
		}
	}
}
?>