<?php
class US23_Parser{
	
	function read_chaplst($chapter_urls){
		//echo "url:".$chapter_urls."<br>";
		$chplst = array();
		$contents = myfile_get_content($chapter_urls);
		$html = str_get_html($contents);
		
		//$$("td.L a")[0]
		foreach($html->find('td.L a') as $chapter) {
			$item['url']     = $chapter_urls.$chapter->href;
			$item['title'] = $chapter->innertext;
			$chplst[] = $item;
		}
		
		//print_r($chplst);
		
		return $chplst;
	}

	function parse_level1($seed_url){
		//$base_domain="http://www.23us.com";
		//echo "pick :".$seed_url."<br>";
		$contents = myfile_get_content($seed_url);
		$html = str_get_html($contents);
		$articles = array();
		// Find all article blocks
		foreach($html->find('tr[bgcolor=\'#FFFFFF\']') as $article) {
			$item['title'] = $article->find('a', 0)->innertext;
			$item['url'] = $article->find('a', 0)->href;
			$item['img']     = '';
			$item['author'] = '';
			$articles[] = $item;
		}
			
		//print_r($articles);
		return $articles;
	}
	
	function parse_level2($article_url){
		//echo "url:".$article_url."<br>";
		$article_info = array();
		
		$chplst = array();
		$contents = myfile_get_content($article_url);
		$html = str_get_html($contents);
		//$$("#at tr")
		$tr = $html->find('#at tr',0);
		$author = $tr->find('td',1)->innertext;
		$status = $tr->find('td',2)->innertext;
		$comment ="";
		//$$("a.read")[0].text
		$chapter_urls = $html->find('a.read',0)->href;
		$chplst= $this->read_chaplst($chapter_urls);
		
		if(strpos($status,"жа")>0){
			$status = 0;
		}else{
			$status = 1;
		}
		
		$article_info['author'] = $author;
		$article_info["status"] = $status;
		$article_info["comment"] = convert($comment);
		$article_info["chplst"] = $chplst;
		
		return $article_info;
		
	}
	
	
	function parse_level3($chapter_url){
		//contents
		$contents = myfile_get_content($chapter_url);
		$html = str_get_html($contents);
		$chpctx = $html->find('#contents',0)->xmltext;
		return $chpctx;
	}
	
}
?>