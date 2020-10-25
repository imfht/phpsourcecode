<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 获取网站SEO详情类
*/

defined('INPOP') or exit('Access Denied');

class RankChecker{
	private $url;
	private $ch;

	public function __construct(){
		$this->ch = curl_init();
	}
	
	//销毁
	public function __destruct(){
		curl_close($this->ch);
	}
	
	private function getContent($url){
		if(!$url) return false;		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_HEADER, 0);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->ch, CURLOPT_REFERER,'http://www.baidu.com');
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Baiduspider+(+http://www.baidu.com/search/spider.htm)");
		$html = curl_exec($this->ch);
		$return = $this->format_html($html);
		return $return;
	}
	
	//格式化HTML并且进行自动转码
	private function format_html($html){
		if ($this->chkCode($html) == "GBK"){
			$html = iconv("gb18030","utf-8",$html);
			$html = '<meta http-equiv="Content-Type" content="text/html;charset=utf-8">'.$html;
			$gb_arr = array("charset=gb18030","charset=gbk","charset=gb2312");
			$html = str_replace($gb_arr,"charset=utf-8",$html);
		}
		return $html;
	}

	private function chkCode($string){
		$code = array('ASCII', 'GBK', 'UTF-8');
		foreach($code as $c){
			if( $string === iconv('UTF-8', $c, iconv($c, 'UTF-8', $string))){
				return $c;
			}
		}
		return null;
	}
	
	//获取alexa排名
	public function getAlexaRank($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$xml = @simplexml_load_string(file_get_contents('http://data.alexa.com/data?cli=10&url=' . $url));
		return $xml ? $xml->SD->POPULARITY['TEXT'] : '';
	}

	//如果被dmoz收录就返回dmoz的目录名称
	public function getDmoz($url){		
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$url = preg_replace('/^www\./', '', $url);
		$url = "http://search.dmoz.org/cgi-bin/search?search=$url";
		$data = $this->getContent($url);
		if(preg_match('<center>No <b><a href="http://dmoz\.org/">Open Directory Project</a></b> results found</center>', $data)){
			$value = false;
		}else{
			$value = true;
		}
		return $value;
	}

	
	//如果被yahoo收录就返回yahoo的目录名称
	public function getYahooDirectory($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$url = preg_replace('/^www\./', '', $url);
		$url = "http://search.yahoo.com/search/dir?p=".$url;
		$data = $this->getContent($url);
		if(preg_match('No Directory Search results were found\.', $data)) {
			$value = false;
		} else {
			$value = true;
		}
		return $value;
	}
	//获取Baidu收录
	public function getIndexedBaidu($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$url = 'http://www.baidu.com/s?wd=site%3A'.urlencode($url);
		$data = $this->getContent($url);
		preg_match('/找到相关结果数([0-9\,]+)个/si', $data, $p);
		$value = isset($p[1]) ? $this->toInt($p[1]) : 0;
		return $value;
	}
	
	//获取google收录
	public function getIndexedGoogle($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];		
		$url = 'http://www.google.com/search?hl=en&safe=off&btnG=Search&q=site%3A'.urlencode($url);
		$data = $this->getContent($url);
		preg_match('/([0-9\,]+) result/si', $data, $p);
		$value = isset($p[1]) ? $this->toInt($p[1]) : 0;
		return $value;
	}
	
	//获取Google反链
	public function getBacklinksGoogle($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$url = 'http://www.google.com/search?q=link%3A'.urlencode($url);
		$data = $this->getContent($url);
		preg_match('/of about \<b\>([0-9\,]+)\<\/b\>/si', $data, $p);
		$value = isset($p[1]) ? $this->toInt($p[1]) : 0;
		return $value;
	}
	
	//获取Yahoo反链
	public function getBacklinksYahoo($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$url = 'http://siteexplorer.search.yahoo.com/search?p='.urlencode($url);
		$data = $this->getContent($url);
		preg_match('/Inlinks \(([0-9\,]+)\)/si', $data, $p);
		$value = isset($p[1]) ? $this->toInt($p[1]) : 0;
		return $value;
	}

	//获取域名年龄
	public function getAge($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$url = preg_replace('/^www\./', '', $url);
		$url = 'http://www.who.is/whois/'.urlencode($url);
		$data = $this->getContent($url);
		preg_match('#(?:Creation Date|Created On):\s*([a-z0-9/-]+)#si', $data, $p);
		if(!isset($p[1])){
			return null;
		}
		$value = time() - strtotime($p[1]);
		return $value;
	}
	
	//获取yahoo的收录数量
	public function getIndexedYahoo($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$url = 'http://siteexplorer.search.yahoo.com/search?p='.urlencode($url);
		$data = $this->getContent($url);
		preg_match('/Pages \(([0-9,]{1,})\)/im', $data, $p);
		$value = isset($p[1]) ? $this->toInt($p[1]) : 0;
		return $value;
	}
	
	//获取PR
	public function getPagerank($url){
		if(!$url) return false;
		$parse_url = parse_url($url);
		$url = $parse_url['host'];
		$chwrite = $this->CheckHash($this->HashURL($url));
		$url="http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".$chwrite."&features=Rank&q=info:".$url."&num=100&filter=0";
		$data = $this->getContent($url);
		preg_match('#Rank_[0-9]:[0-9]:([0-9]+){1,}#si', $data, $p);
		$value = isset($p[1]) ? $p[1] : 0;
		return $value;
	}
	
	private function toInt($string){
		return preg_replace('#[^0-9]#si', '', $string);
	}

	//--> for google Piwik_SEO_Ranks
	private function StrToNum($Str, $Check, $Magic){
		$Int32Unit = 4294967296; // 2^32
		$length = strlen($Str);
		for($i = 0; $i < $length; $i++){
			$Check *= $Magic;
			// If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31),
			// the result of converting to integer is undefined
			// refer to http://www.php.net/manual/en/language.types.integer.php
			if($Check >= $Int32Unit){
				$Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
				//if the check less than -2^31
				$Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
			}
			$Check += ord($Str{$i});
		}
		return $Check;
	}

	//Genearate a hash for a url
	private function HashURL($String){
		$Check1 = $this->StrToNum($String, 0x1505, 0x21);
		$Check2 = $this->StrToNum($String, 0, 0x1003F);

		$Check1 >>= 2;
		$Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
		$Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
		$Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);

		$T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
		$T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );

		return ($T1 | $T2);
	}

	//--> for google Piwik_SEO_Ranks
	private function CheckHash($Hashnum){
		$CheckByte = 0;
		$Flag = 0;
		$HashStr = sprintf('%u', $Hashnum) ;
		$length = strlen($HashStr);
		for($i = $length - 1; $i >= 0; $i --){
			$Re = $HashStr{$i};
			if(1 === ($Flag % 2)) {
				$Re += $Re;
				$Re = (int)($Re / 10) + ($Re % 10);
			}
			$CheckByte += $Re;
			$Flag ++;
		}

		$CheckByte %= 10;
		if(0 !== $CheckByte){
			$CheckByte = 10 - $CheckByte;
			if(1 === ($Flag % 2) ){
				if(1 === ($CheckByte % 2)){
					$CheckByte += 9;
				}
				$CheckByte >>= 1;
			}
		}

		return '7'.$CheckByte.$HashStr;
	}
}
?>