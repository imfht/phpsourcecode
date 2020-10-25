<?php
/**
 * 获取关键字的排名信息
 * @author 贾军军
 * 
 */
class GetRank
{
	public $charset;
	public $timeout = 10;
	public $error;
	public $errorinfo;
	public $url;
	public $rankurl;
	public $content;
	public $keywords = array ();
	public $metainfo = array ();
	public $method;
	
	/**
	 * 初始化对象时必须传递URL,字符编码如果不传递，默认为UTF-8<br>
	 * 初始化的同时还调用系统函数get_meta_tags来获取网站的meta信息<br>
	 * get_meta_tags中的meta信息必须在head之间，否则无法获取<br>
	 * 获取之后根据传入的编码进行转码<br>
	 * 
	 * @param string $charset        	
	 * @param string $url        	
	 * @param string $content        	
	 */
	public function __construct($url, $charset = 'UTF-8',$method='all')
	{
		$this->charset = $charset;
		$this->url = $url;
		$this->method = $method;
		$this->metainfo['charset'] = $charset;
	}
	/**
	 * 获取网站的关键词描述信息
	 */
	public function getKeywords()
	{
		$arr = get_meta_tags ( $this->url );
		print_r($arr);
		if ($this->charset != 'UTF-8')
		{
			$this->metainfo ['keywords'] = iconv ( $this->charset, 'UTF-8', trim ( $arr ['keywords'] ) );
			$this->metainfo ['description'] = iconv ( $this->charset, 'UTF-8', trim ( $arr ['description'] ) );
		}
		else
		{
			$this->metainfo ['keywords'] = trim ( $arr ['keywords'] );
			// 获取网站的描述信息
			$this->metainfo ['description'] = trim ( $arr ['description'] );
		}
		self::getArrKey ();
	}
	/**
	 * 获取指定URL的标题
	 */
	public function getTitle()
	{
		//self::getContent($this->url);
		$pattern = '/<title>(.*)<\/title>/';
		$matches = array ();
		if (! $this->content)
		{
			$this->metainfo ['title'] = "获取失败";
		}
		else
		{
			preg_match_all ( $pattern, $this->content, $matches );
			$this->metainfo ['title'] = iconv ( $this->charset, 'UTF-8', trim ( $matches [1] [0] ) );
		}
	}
	
	public function getSnapshot()
	{
		$pattern = '/<table.*?id="1".*?>(.*?)<\/table>/';
		//$matches = array();
		self::getContent();
		preg_match_all($pattern, $this->content, $matches);
		//print_r($matches);exit;
	}
	/**
	 * 获取指定URL内容，如果失败返回错误代码
	 */
	public function getContent($url='')
	{
		if ($url == '')$url = $this->url;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $this->timeout );
		curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.0)" );
		curl_setopt ( $ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // 是否抓取跳转后的页面
		
		$output = curl_exec ( $ch );
		//var_dump($output);
		//echo curl_errno ( $ch )."--".curl_error ( $ch );exit;
		
		if ($output === FALSE)
		{
			//echo curl_errno ( $ch )."--".curl_error ( $ch );
			//$this->content = curl_errno ( $ch ) . ":" . curl_error ( $ch );
			$this->error = curl_errno ( $ch ) . "|" . curl_error ( $ch );
		}
		else
		{
			curl_close ( $ch );
			$this->content = $content = str_replace(array("\r\n","\r","\n","&nbsp;&nbsp;","  "),'',$output);
		}
	}
	/**
	 * 正则匹配结果，需要经过四次匹配
	 * 1.匹配table的ID及整个table的内容----$matches
	 * 2.遍历$matches，将匹配的ID作为索引，内容为值，存入数组－－－－－－$new
	 * 3.遍历新获得的数组$new，匹配出URL地址、收录时间、内容标题、真实地址等信息-------$new2
	 * 4.遍历新获得的数组$new2，返回其真实URL与传递的URL相同的元素索引值
	 * 
	 * @param string $key        	
	 */
	public function getSort($key, $url = '')
	{
		if ($url == '') $url = $this->url;

		if ($this->method == 'all')
		{
			self::getBaiDu ( $key );
			if(!$this->error)
				return self::doRegx($url);
			else
				return $this->errorinfo;
			
		}
		elseif ($this->method == 'each')
		{
			for($i = 1; $i < 11; $i ++)
			{
				self::getBaiDu ( $key, $i );
				//echo $this->content;
				$p = self::doRegx($url);
				if ($p != 0)
				{
					//$p = $p+(($i-1)*10);
					break;
				}
			}
			if ($p != 0)
			{
				return $p;
			}
			else return 0;
		}
	
	}
	
	public function doRegx($url)
	{
		$pattern = '/<table.*?id="([0-9]{1,3})".*?>(.*?)<\/table>/i';
		$url2 = str_replace(array('http:','/','www'),'',$url);
		$matches = array ();
		preg_match_all ( $pattern, $this->content, $matches );
		
		$pattern_titleAndBaiduURL = '/<a.*?href="(.*)".*?target="_blank" >(.*)<\/a>/iU';
		
		$pattern_timeAndRealURL = '/<span class="g">(.*?)<\/span>/i';

		if ($matches)
		{
			for($i = 0; $i < count ( $matches [1] ); $i ++)
			{
				$arr [$matches [1] [$i]] = $matches [2] [$i];
			}
			
			if ($arr)
			{
				$new_arr = array ();
				foreach ( $arr as $k => $v )
				{
					//preg_match_all ( $pattern_titleAndBaiduURL, $v, $titleAndBaiduURL );
					preg_match_all ( $pattern_timeAndRealURL, $v, $timeAndRealURL );
					// 匹配加密的百度URL地址
					//@$new_arr [$k] ['baidulink'] = $titleAndBaiduURL [1] [0];
					// 匹配地址中的文本并去除HTML标签
					//@$new_arr [$k] ['title'] = strip_tags ( $titleAndBaiduURL [2] [0] );
					// 匹配收录时间以空格分割，存入数组
					//print_r($timeAndRealURL);
					@$t = explode ( ' ', trim ( $timeAndRealURL [1] [0] ) );
					// 获取时间后面的不完整地址，可以看作未加密的URL地址
					$new_arr [$k] ['reallink'] = strip_tags ( trim ( $t [0] ) );
					// 获取网页的收录时间
					@$new_arr [$k] ['time'] = trim ( $t [1] );
				}
				//print_r($new_arr);exit;
				if ($new_arr)
				{
					foreach ( $new_arr as $k => $v )
					{
						
						// 如果未加密的URL地址去掉前面的http://与传递的URL地址相同，则返回其索引值并跳出循环
						//if ($v ['reallink'] == mb_substr ( $url, 7 ))
						if(strpos($v ['reallink'],$url2))
						{
							$p = $k;
							
							break;
						}
					}
				}
			}
		}
		
		if (! isset ( $p ))
		{
			return 0;
		}
		else
		{
			return $p;
		}
	}
	
	private function __get($name)
	{
		return $this->$name;
	}
	
	private function getBaiDu($key, $n = 100)
	{
		if ($n != 100)
		{
			$n = ($n-1)*10;
			$str = "&pn=".$n;
		}else
		{
			$str = "&rn=100";
		}
		$this->rankurl = "http://www.baidu.com/s?q1=" . urlencode($key) . $str ;
		//echo $this->rankurl;
		self::getContent ($this->rankurl);
	}
	
	private function getArrKey()
	{
		$string = str_replace ( '，', ',', $this->metainfo ['keywords'] );
		$pattern = '/[\s,_]/';
		$this->keywords = preg_split ( $pattern, $string );
	}
}