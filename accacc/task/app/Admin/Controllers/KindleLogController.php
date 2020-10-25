<?php

namespace App\Admin\Controllers;

use App\Models\KindleLog;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class KindleLogController extends Controller {
	use ModelForm;
	
	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content ( function (Content $content) {
			
			$content->header ( 'header' );
			$content->description ( 'description' );
			
			$content->body ( $this->grid () );
		} );
	}
	
	/**
	 * Edit interface.
	 *
	 * @param
	 *        	$id
	 * @return Content
	 */
	public function edit($id) {
		return Admin::content ( function (Content $content) use ($id) {
			
			$content->header ( 'header' );
			$content->description ( 'description' );
			
			$content->body ( $this->form ()->edit ( $id ) );
		} );
	}
	
	/**
	 * Create interface.
	 *
	 * @return Content
	 */
	public function create() {
		return Admin::content ( function (Content $content) {
			
			$content->header ( 'header' );
			$content->description ( 'description' );
			
			$content->body ( $this->form () );
		} );
	}
	
	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid ( KindleLog::class, function (Grid $grid) {
			
			$grid->id ( 'ID' )->sortable ();
			
			$grid->type ( '类型' );
			$grid->user ()->name ( '创建者' );
			$grid->status ( '状态' );
			$grid->path ( '文件路径' );
			$grid->created_at ( '创建时间' );
			
			$grid->disableActions ();
			$grid->disableCreation ();
			$grid->disableRowSelector ();
		} );
	}
	
	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form ( KindleLog::class, function (Form $form) {
			
			$form->display ( 'id', 'ID' );
			
			$form->display ( 'created_at', 'Created At' );
			$form->display ( 'updated_at', 'Updated At' );
		} );
	}
}
