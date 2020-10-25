<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Utils\ErrorCodeUtil;

/**
 * 订阅分类控制器
 *
 * @author edison.an
 *        
 */
class CategoryController extends Controller {
	
	/**
	 * CategoryService 实例.
	 *
	 * @var CategoryService
	 */
	protected $categoryService;
	
	/**
	 * 构造方法
	 *
	 * @param CategoryService $categoryService        	
	 * @return void
	 */
	public function __construct(CategoryService $categoryService) {
		$this->middleware ( 'auth' );
		
		$this->categoryService = $categoryService;
	}
	
	/**
	 * 展示分类首页
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		$categorys = $this->categoryService->getList ( $request->user (), $needPage = true );
		
		return view ( 'categorys.index', [ 
				'categorys' => $categorys 
		] );
	}
	
	/**
	 * 保存分类
	 *
	 * @param Request $request        	
	 */
	public function store(Request $request) {
		$this->validate ( $request, [ 
				'name' => 'required' 
		] );
		
		$category = $request->user ()->categorys ()->create ( $request->all () );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE, $category );
			return response ( $resp );
		} else {
			return redirect ( '/categorys' )->with ( 'message', 'IT WORKS!' );
		}
	}
	
	/**
	 * 删除分类
	 *
	 * @param Request $request        	
	 * @param Category $category        	
	 */
	public function destroy(Request $request, Category $category) {
		$this->authorize ( 'destroy', $category );
		
		if (empty ( $category->feeds ) || count ( $category->feeds ) == 0) {
			$category->delete ();
		} else {
			$resp = $this->responseJson ( 1000, null, 'This category has Feeds!cannot delete!' );
			return response ( $resp );
		}
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/categorys' )->with ( 'message', 'IT WORKS!' );
		}
	}
	
	/**
	 * 更新分类
	 *
	 * @param Request $request        	
	 * @param Category $category        	
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function update(Request $request, Category $category) {
		$this->authorize ( 'destroy', $category );
		
		if ($request->method () == 'GET') {
			return view ( 'categorys.update', array (
					'category' => $category 
			) );
		}
		
		$this->validate ( $request, [ 
				'name' => 'required' 
		] );
		
		$category->update ( $request->all () );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/categorys' )->with ( 'message', 'IT WORKS!' );
		}
	}
	
	/**
	 * 设置订阅分类的排序
	 *
	 * @param Request $request        	
	 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	public function sort(Request $request) {
		$this->validate ( $request, [ 
				'category_ids' => 'required' 
		] );
		
		$categoryIds = explode ( ',', $request->category_ids );
		$this->categoryService->setCategorySort ( $request->user (), $categoryIds );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/categorys' )->with ( 'message', 'IT WORKS!' );
		}
	}
}
