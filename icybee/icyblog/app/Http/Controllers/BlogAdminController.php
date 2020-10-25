<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\blog\BlogContentService;

class BlogAdminController extends Controller {

	public function __construct(BlogContentService $blog)
        {
		$this->middleware('auth');
                $this->blog = $blog;
        }

	public function frontpage()
	{
		//	
		return self::index(1);
	}

	public function addLabel($name){
		$result = $this->blog->addLabel($name);
		return $result;
	}
	
	public function deleteLabel($name){
		$result = $this->blog->deleteLabel($name);
		return $result;
	}
	
	public function addArticleLabel(Request $request){
		$id 	= $request->get('articleid',0);
		$label	= $request->get('label','');
		$result = $this->blog->addArticleLabel($id,$label);
		return $label;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($page)
	{
		//	
		$pageInfo 	= $this->blog->getAllPost($page,10);
		$pageCount	= $this->blog->getAllPageCount(10);
		$labels		= $this->blog->getAllLabel();
		$pageHitCount	= $this->blog->getCacheCountByTime(date('Y-m-d'),'page');
		$articleHitCount= $this->blog->getCacheCountByTime(date('Y-m-d'),'article');
		return view('blogmanage',[
			'content'	=> $pageInfo,
			'pages'		=> $pageCount,
			'currentpage'	=> $page,
			'labels'	=> $labels,
			'pageHitCount'	=> $pageHitCount,
			'articleHitCount'=>$articleHitCount,
		]);

	}

	public function delete(Request $request){
		$id = $request->get('id',0);
		$arrServiceOutput = $this->blog->deleteArticle($id);
		return json_encode($arrServiceOutput);
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
