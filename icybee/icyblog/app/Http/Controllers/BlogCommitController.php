<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Article;
use App\Services\blog\BlogContentService;

use Illuminate\Http\Request;

class BlogCommitController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request,Article $article,BlogContentService $contentService)
	{
		//
		$title 		= $request->get('title');
		$body		= $request->get('body'); 
		$id		= $request->get('id');
		$state		= $request->get('state','posted');
		$labels		= $request->get('labels');
		$bodyhtml	= $request->get('bodyhtml');
		$summaryhtml	= $request->get('summaryhtml');
		$user_id = 1;
		$result = $contentService->addPost($title,$body,$user_id,$id,$state,$labels,$bodyhtml,$summaryhtml);
		
		return $result;
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
