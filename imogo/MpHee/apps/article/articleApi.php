<?php
class articleApi extends baseApi{
	
	public function getMenu(){
		return array(
					'sort'=>1,
					'title'=>'素材管理',					
					'list'=>array(
						'首次关注'=>url('article/index/guanzhu'),
						'关键字回复'=>url('article/index/keyword'),
						'图文管理页面'=>url('article/index/article'),
					)
			);
	}
	
	public function Reply($ppid,$revdata){
		if( $revdata['Event'] == 'subscribe'){
			$subscribe = $this->model->table('sucai_guanzhu')->where(array('ppid'=>$ppid))->find();
			if( $subscribe['type'] == 1 ){
				return $subscribe['content'];
			}elseif( $subscribe['type'] == 2 ){
				return $article = $this->getArticle($subscribe['articleid'],$revdata['FromUserName']);
			}
		}elseif( $revdata['MsgType'] == 'text'){
			$keyword = $this->model->table('sucai_keyword')->where(array('keyword'=>$revdata['Content'],'ppid'=>$ppid))->find();
			if( $keyword['type'] == 1 ){
				return $keyword['content'];
			}elseif( $keyword['type'] == 2 ){
				return $article = $this->getArticle($keyword['articleid'],$revdata['FromUserName']);
			}
		}elseif( $revdata['Event'] == 'CLICK' ){
			$keyword = $this->model->table('sucai_keyword')->where(array('keyword'=>$revdata['EventKey'],'ppid'=>$ppid))->find();
			if( $keyword['type'] == 1 ){
				return $keyword['content'];
			}elseif( $keyword['type'] == 2 ){
				return $article = $this->getArticle($keyword['articleid'],$revdata['FromUserName']);
			}
		}
	}
	
	public function getArticle($articleid,$FromUserName){
		$article = $this->model->table('sucai_article')->where( array('id'=>$articleid) )->find();
		if($article['type'] == 1){
			    if(empty($article['con'])){
					if(strpos($article['url'], '?')){
					    $url = $article['url'].'&uuid='.$FromUserName;
					}else{
					    $url = $article['url'].'?uuid='.$FromUserName;
					}
				}else{
					$url = 'http://'.$_SERVER['HTTP_HOST'].url('mobile/show',array(id=>$article['id'],uuid=>$FromUserName));
				}
				return array(
				    "0"=>array(
					'Title'=>$article['tit'],
					'Description'=>$article['desc'],
					'PicUrl'=>'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/'.$article['pic'],
					'Url'=>$url
					),
				);
		}elseif($article['type'] == 2){
			    $replymul = array();
				$item = array();
				$item["Title"] = $article["tit"];
				$item["Description"] = $article['desc'];
				$item["PicUrl"] = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/'.$article['pic'];
				if(empty($article['con'])){
				    if(strpos($article['url'], '?')){
					    $item["Url"] = $article['url'].'&uuid='.$FromUserName;
					}else{
					    $item["Url"] = $article['url'].'?uuid='.$FromUserName;
					}
				}else{
				    $item["Url"] = 'http://'.$_SERVER['HTTP_HOST'].url('mobile/show',array(id=>$article['id'],uuid=>$FromUserName));
				}
				$replymul[] = $item;
				
				$sublist = $this->model->table('sucai_article')->where( array('pid'=>$articleid) )->select();
				if (is_array($sublist))
				{
					foreach ($sublist as $subinfo)
					{
						$item = array();
						$item["Title"] = $subinfo["tit"];
						$item["Description"] = $subinfo['desc'];
						$item["PicUrl"] = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/'.$subinfo['pic'];
						if(empty($subinfo['con'])){
						    if(strpos($subinfo["url"], '?')){
							    $item["Url"] = $subinfo["url"].'&uuid='.$FromUserName;
							}else{
							    $item["Url"] = $subinfo["url"].'?uuid='.$FromUserName;
							}
						}else{
					        $item["Url"] = 'http://'.$_SERVER['HTTP_HOST'].url('mobile/show',array(id=>$subinfo["id"],uuid=>$FromUserName));
						}
						$replymul[] = $item;
					}
				}
				return $replymul;
		}
	}
	
}