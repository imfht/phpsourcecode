<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\blog\BlogContentService;
class BlogPublishController extends Controller {

	public function __construct(BlogContentService $blog)
        {
		$this->middleware('auth');
		$this->blog = $blog;
        }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$labels = $this->blog->getAllLabel();
		return view('blogpublish',[
			'labels'	=> $labels,
			'articlelabels'	=> array(),
		]);
	}

	public function editArticle($id)
	{
		$content 	= $this->blog->getArticleDet($id);
		$labels		= $this->blog->getAllLabel();
		$articlelabels	= $this->blog->getArticleLabels($id);

		$tmpArtLabels	= array();
		foreach($articlelabels as $one){
			$tmpArtLabels[] = $one['label'];
		}
		$articlelabels	= $tmpArtLabels;

		return view('blogpublish',[
			'content'	=> $content['body'],
			'title'		=> $content['title'],
			'id'		=> $content['id'],
			'state'		=> $content['state'],
			'labels'	=> $labels,
			'articlelabels'	=> $articlelabels,
			
		]);
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
