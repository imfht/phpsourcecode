<?php namespace App\Services\blog;

use App\Article;
use App\Label;
use App\ArticleLabel;
use App\Services\common\Redis;

class BlogContentService {

	public function test()
	{
		return "shi......it,you got me!";
	}
	
	public function addPost($title,$body,$user_id,$article_id,$state,$labels,$bodyhtml,$summaryhtml){
		if(!$article_id){
			$result = Article::create([
				'title'		=> $title,
				'body'		=> $body,
				'user_id'	=> $user_id,
				'state'		=> $state,
				'bodyhtml'	=> $bodyhtml,
				'summaryhtml'	=> $summaryhtml,
			]);	
			$article_id = $result['id'];
		}else{
			$result = Article::where('id',$article_id)->update([
				'title'		=> $title,
				'body'		=> $body,
				'user_id'	=> $user_id,
				'state'		=> $state,
				'bodyhtml'	=> $bodyhtml,
				'summaryhtml'	=> $summaryhtml,
			]);	
		}
		ArticleLabel::where('articleid',$article_id)->delete();
		if(!empty($labels)){
			foreach($labels as $label){
				ArticleLabel::create([
					'articleid'	=> $article_id,
					'label'		=> $label,
				]);
			}
		}
		self::clearPageCache();	
		self::clearArticleCache();
		return $result;	
	}

	public function getAllLabel(){
		return Label::get();
	}
	
	public function addLabel($strName){
		$result = Label::create([
			'name'	=> $strName,
		]);
		self::clearPageCache();	
		self::clearArticleCache();	
		return $result;
	}
	
	public function deleteLabel($strName){
		$result = Label::where('name',$strName)->delete();
		self::clearPageCache();	
		self::clearArticleCache();	
		return $result;
	}
	public function getArticleLabels($id){
		return ArticleLabel::where('articleid',$id)->select('label')->get();
	}

	public function addArticleLabel($id,$label){
		$result = ArticleLabel::create([
			'articleid'	=> $id,
			'label'		=> $label,
		]);	
		self::clearArticleCache();	
		return json_encode($result);
	}

	/**
	* @Input
	* 	page 	: the page NO.
	*	prepage	: how many article pre page.
	*/
	public function getAllPost($page,$prepage){
		return Article::orderBy('created_at','DESC')->take($prepage)->offset(($page - 1) * $prepage)->get();
	}

	public function getPostedPage($page,$prepage,$label=null){
		if(empty($label)){
			return Article::orderBy('created_at','DESC')->where('state','posted')->take($prepage)->offset(($page - 1) * $prepage)->get();
		}else{
			$result = ArticleLabel::where('label',$label)->join('articles','articles.id','=','article_labels.articleid')->select('articles.*')->orderBy('articles.created_at','DESC')->where('state','posted')->take($prepage)->offset(($page - 1) * $prepage)->get();
			//return Article::orderBy('created_at','DESC')->where('state','posted')->take($prepage)->offset(($page - 1) * $prepage)->get();
			return $result;
		}
	}

	public function getPostedPageCount($prepage,$label=null){
		if(empty($label)){
			return ceil(Article::where('state','posted')->count() / $prepage);
		}else{
			$result = ceil(ArticleLabel::join('articles','articles.id','=','article_labels.articleid')->where('article_labels.label',$label)->where('articles.state','posted')->count() / $prepage);		
			return $result;
		}
	}

	public function getAllPageCount($prepage){
		return ceil(Article::count() / $prepage);
	}

	public function getArticle($articleid){
		$result = Article::where('id',$articleid)->orWhere('title',$articleid)->get()[0];
		if(isset($result['body'])){
			return $result['body'];
		}else{
			return '';
		}
	}
	

	public function getArticleInfo($articleid){
		$result = Article::where('id',$articleid)->orWhere('title',$articleid)->get()[0];
		if(isset($result['body'])){
			return $result;
		}else{
			return null;
		}
	}
	

	public function getArticleDet($articleid){
		$result = Article::where('id',$articleid)->get()[0];
		return $result;	
	}
	
	public function deleteArticle($articleid){
		$result = Article::where('id',$articleid)->delete();
		self::clearPageCache();
		self::clearArticleCache();
		return $result;
	}

	public function setPageCache($label,$page,$html){
		$redisKey = 'blogPageCache';
		$cacheKey = '__label__'.strval($label).'__page__'.strval($page);
		$value = Redis::get($redisKey);
		if($value === null){
			$value = array(
				$cacheKey	=> $html,
			);
		}else{
			$value[$cacheKey]	= $html;
		}
		$result = Redis::set($redisKey,$value);

		return $result;
	}

	public function getPageCache($label,$page){
		$redisKey = 'blogPageCache';
		$cacheKey = '__label__'.strval($label).'__page__'.strval($page);
		$value = Redis::get($redisKey);
		if(null === $value || !isset($value[$cacheKey])){
			self::addHitCount('page',false);
			return null;
		}else{
			self::addHitCount('page',true);
			return $value[$cacheKey];
		}
	}

	public function clearPageCache(){
		$redisKey = 'blogPageCache';
		$result = Redis::delete($redisKey);
		return $result;
	}

	public function setArticleCache($id,$html,$kind='html'){
		$redisKey = 'blogArticleCache';
		$cacheKey = '__id__'.strval($id).'__kind__'.strval($kind);
		$value = Redis::get($redisKey);
		if($value === null){
			$value = array(
				$cacheKey	=> $html,
			);
		}else{
			$value[$cacheKey]	= $html;
		}
		$result = Redis::set($redisKey,$value);

		return $result;
	}

	public function getArticleCache($id,$kind='html'){
		$redisKey = 'blogArticleCache';
		$cacheKey = '__id__'.strval($id).'__kind__'.strval($kind);
		$value = Redis::get($redisKey);
		if(null === $value || !isset($value[$cacheKey])){
			self::addHitCount('article',false);
			return null;
		}else{
			self::addHitCount('article',true);
			return $value[$cacheKey];
		}
	}
	
	public function clearArticleCache($id = null){
		$redisKey = 'blogArticleCache';
		$result = Redis::delete($redisKey);
		return $result;
	}

	public function addHitCount($kind='page',$hit = true){
		$redisKey = '__hitcount__'.$kind;
		$cacheKey = date('Y-m-d');
		$val = Redis::get($redisKey);
		if(false === $val){
			$val = array(
				$cacheKey => array(
					'hit'	=> $hit?1:0,
					'miss'	=> $hit?0:1,
				)
			);
		}else if(!isset($val[$cacheKey])){
			$val[$cacheKey] = array(
					'hit'	=> $hit?1:0,
					'miss'	=> $hit?0:1,
				);
		}else{
			if($hit){
				$val[$cacheKey]['hit'] ++;
			}else{
				$val[$cacheKey]['miss'] ++;
			}
		}
		$result = Redis::set($redisKey,$val);
		return $result;
	}
	
	public function getCacheCountByTime($time,$kind='page'){
		$redisKey 	= '__hitcount__'.$kind;
		$val 		= Redis::get($redisKey);
		if(!isset($val[$time])){
			return null;
		}
		$static = $val[$time];
		if($static['hit'] + $static['miss'] == 0){
			$hitRate = 0;
		}else{
			$hitRate = 100 * $static['hit']/($static['hit'] + $static['miss']);
			$hitRate = round($hitRate,2);
		}
		$static['hitRate'] = $hitRate;
		return $static;
	}

}
