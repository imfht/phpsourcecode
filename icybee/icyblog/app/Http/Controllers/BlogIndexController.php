<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\blog\BlogContentService;

class BlogIndexController extends Controller {


	public function __construct(BlogContentService $blog)
	{
		$this->blog = $blog;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($label,$page)
	{	

		$redisResult = $this->blog->getPageCache($label,$page);

		if(null !== $redisResult){
			return $redisResult;
		}

		$content = $this->blog->getPostedPage($page,8,$label);
		foreach($content as &$one){
			// prettify the datetime interface
			$datetime 	= $one['created_at'];
			$datestr	= self::getDateStr($datetime); 
			$one['time']	= $datestr;
		
			// get the summary
			$one['brief']	= self::getSummary($one['body']);

			// get the words count
			$one['words']	= self::getWordCount($one['body']);
			
		}
		$htmlResult = view('blogindex',
			[
				'content'	=> $content,
				'pages'		=> $this->blog->getPostedPageCount(8,$label),
				'currentpage'	=> $page,
				'label'		=> $label,
				'labels'	=> $this->blog->getAllLabel(),
			]
		);	
		$this->blog->setPageCache($label,$page,strval($htmlResult));
		return $htmlResult;
	}

	public function frontIndex(){
		return self::index(null,1);
	}

	public function pageIndex($page){
		return self::index(null,$page);
	}

	public function labelIndex($label){
		return self::index($label,1);
	}

	private function getDateStr($datetime){
		$date 		= substr($datetime,0,10);
		$year 		= substr($date,0,4);
		$month 		= substr($date,5,2);
		$day		= substr($date,8,2);
		$datestr	= self::getStar($datetime).", ".self::convertMonth($month)." ".$day.", ".$year; 
		return $datestr;
	}

	private function getWordCount($str){
		$str = preg_replace('/[\x80-\xff]{1,3}/', ' Chn ', $str, -1);
		return str_word_count($str);	
	}

	private function getSummary($str){
		//confs
		$maxWord = 180;
		$maxLine = 6;

		$pieces 	= explode("\n",$str);
		$lines 		= count($pieces);
		$nowLine 	= 1;
		$nowWord 	= 0;

		$result		= '';
		while($nowLine <= $lines && $nowLine < $maxLine && $nowWord < $maxWord){
			$result = $result.$pieces[$nowLine - 1]."\n";
			$nowWord +=  self::getWordCount($pieces[$nowLine - 1]);
			$nowLine ++;
		}
		return $result;
	}	

	private function getStar($datetime)
	{
	    $weekday = date('w', strtotime($datetime));
	    return  ['日', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$weekday];
	}

	private function convertMonth($numMonth){
		switch($numMonth){
			case 1:return "Jan";
			case 2:return "Feb";
			case 3:return "Mar";
			case 4:return "Apr";
			case 5:return "May";
			case 6:return "Jun";
			case 7:return "Jul";
			case 8:return "Aug";
			case 9:return "Sep";
			case 10:return "Oct";
			case 11:return "Nov";
			case 12:return "Dec";
			default:return "Unknown";
		}
	}
	public function article($id)
	{
		$redisResult = $this->blog->getArticleCache($id);

		if(null !== $redisResult){
			return $redisResult;
		}

		$body = $this->blog->getArticleInfo($id);
		$body['time'] = self::getDateStr($body['created_at']); 
		$labels = $this->blog->getArticleLabels($body['id']);
		$page = view('articleindex',[
				'id' 		=> $body['id'],
				'article'	=> $body,
				'labels'	=> $labels,
				'allLabels'	=> $this->blog->getAllLabel(),
			]
		);	
		
		$this->blog->setArticleCache($id,strval($page));
		return $page;
	}

	public function articleJson(Request $request)
	{

		$id = $request->get('id',1);

		$redisResult = $this->blog->getArticleCache($id,'json');

		if(null !== $redisResult){
			return $redisResult;
		}

		$body = $this->blog->getArticle($id);

		$this->blog->setArticleCache($id,strval($body),'json');
		return $body;
	}

	public function articleSummary(Request $request){
		$id = $request->get('id',1);
		$body = $this->blog->getArticle($id);
		return self::getSummary($body);
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
