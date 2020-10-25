<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ArticleController extends Controller {
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
		return Admin::grid ( Article::class, function (Grid $grid) {
			
			$grid->id ( 'ID' )->sortable ();
			
			$grid->subject ( '文章名' )->display ( function ($name) {
				return "<a target='_blank' href='/article/view/{$this->id}'>$name</span>";
			} );
			$grid->feed ()->feed_name ( '订阅源' )->limit ( 50 );
			$grid->published ( '发布时间' );
			
			$grid->created_at ( '录入时间' );
			
			$grid->disableActions ();
			$grid->disableCreation ();
			$grid->disableRowSelector ();
			
			$grid->filter ( function ($filter) {
				
				$filter->like ( 'subject', 'subject' );
				$filter->like ( 'content', 'content' );
			} );
		} );
	}
	
	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form ( Article::class, function (Form $form) {
			
			$form->display ( 'id', 'ID' );
			
			$form->display ( 'created_at', 'Created At' );
			$form->display ( 'updated_at', 'Updated At' );
		} );
	}
}
