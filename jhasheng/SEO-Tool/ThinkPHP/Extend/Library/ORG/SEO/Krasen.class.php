<?php
/**
 * @author 贾军军
 *	功能：获取网站的内容等信息
 *	执行过程：
 *	1.初始化类，传递url,同时获取网站的状态码，并根据状态码处理URL
 *	2.获取网站的编码
 *	3.获取网站的内容代码部分
 */
class Krasen
{
	private $charset = 'unknown';
	
	private $statuscode = 'unknown';
	
	private $url = NULL;
	
	private $realurl = NULL;
	
	private $timeout = 3;
	
	private $snapshot = '无快照';
	
	private $domain = NULL;
	
	private $pages = 0;
	
	private $unlinks = 0;
	
	private $title = '';
	
	private $keywords = '';
	
	private $description = '';
	
	private $error = '';
	
	private $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)";
	
	// public $webinfo = array();
	/**
	 * 初始化对象时，调用所有的私有方法，将所有的私有变量放入数组内
	 *
	 * @param string $url        	
	 * @param int $timeout        	
	 */
	public function __construct($url, $timeout = 10)
	{
		$this->timeout = $timeout;
		$this->url = $this->realurl = $url;
	
	}
	
	public function getSnapshot()
	{
		$url = 'http://www.baidu.com/s?wd=' . trim ( isset($this->realurl)?$this->realurl:$this->url );
		// 获取内容
		$content = self::getContent ( $url );
		if ($content)
		{
			$matches = $matches2 = array ();
			if (! preg_match ( '/没有找到该URL。您可以直接访问/i', $content ))
			{
				$pattern = '/<table.*?id="1".*?>(.*?)<\/table>/';
				// 匹配第一个结果，如果内容为空，则无快照
				$content = str_replace(array("\r\n","\r","\n","&nbsp;&nbsp;"),'&nbsp;',$content);
				preg_match_all ( $pattern, $content, $matches );
				
				if (!isset($matches [1] [0]))
				{
					$this->domain = 'http://';
					$this->snapshot = '0000-00-00';
					return false;
				}
				//匹配结果中的域名及时间
				$pattern = '/<span class="g">(.*?)<\/span>/i';
				preg_match_all ( $pattern, $matches [1] [0], $matches2 );
				
				$rs_arr = explode ( '&nbsp;', strip_tags ( $matches2 [1] [0] ) );

				$this->domain = trim ( $rs_arr [0] );
				$this->snapshot = trim ( $rs_arr [1] );
			}
			else
			{
				$this->snapshot = '0000-00-00';
				return false;
			}
		}
	}
	
	public function getPages()
	{
		$pattern = '/相关结果数([\d,]+)个/';
		$name = "pages";
		$type = "site";
		$this->getNums($type, $pattern,$name);
	}
	
	public function getUnlinks()
	{
		$pattern = '/百度为您找到相关结果[约]+([\d,]+)个/';
		$name = "unlinks";
		$type = "domain";
		$this->getNums($type, $pattern,$name);
	}
	
	private function getNums($type,$pattern,$name)
	{
		$sub_url = substr($this->realurl,7);
		$url = "http://www.baidu.com/s?wd=".$type.":".trim($sub_url,'/');

		$subject = self::getContent($url);
		preg_match_all($pattern, $subject, $matches);

		if(isset($matches[1][0]))
		{
			$this->$name = $matches[1][0];
		}
	}
	
	public function getReal()
	{
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_URL, $this->url );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, $this->timeout );
		curl_setopt ( $curl, CURLOPT_HEADER, 1 );
		curl_setopt ( $curl, CURLOPT_USERAGENT, $this->agent );
		curl_setopt ( $curl, CURLOPT_ENCODING, 'gzip' );
		//curl_setopt ( $curl, CURLOPT_NOBODY, true ); // 采集时排除网站body部分
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, 'GET' ); // 如果设置了nobody-ture,则有此项不然会因为http服务器不允许
		                                                     // HEAD 命令而返回403错误
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($curl, CURLOPT_RANGE, "100000-");
		$content = curl_exec ( $curl );

		if ($content === FALSE)
		{
			curl_close ( $curl );
			$this->realurl = $this->url;
			$this->error = curl_errno ( $ch ) . "|" . curl_error ( $ch );
			return false;
		}
		else
		{
			$this->statuscode = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
			//部分ISP提供商在访问不存在的域名时会有302跳转，如电信会跳转到114；有些域名会有301跳转
			if ($this->statuscode == 301 || $this->statuscode == 302)
			{
				preg_match ( '/Location:(.*?)\n/', $content, $matches );
				
				$this->realurl = trim ( $matches [1] );
				$content = $this->getContent($this->realurl);
			}
			else
			{
				$this->realurl = $this->url;
			}
			self::getCharset($content);
			self::getTitle($content);
			curl_close ( $curl );
			return true;
		}
	
	}
	
	public function getCharset($content)
	{
		$pattern = '/<meta.+?charset=[^\w]?([-\w]+)/i';
		//$content = self::getContent ( $this->realurl );
		$matches = array ();
		if (!$content)
		{
			return false;
		}
		else
		{
			preg_match_all ( $pattern, $content, $matches );
			
			if (count ( $matches [1] ) > 0)
			{
				$this->charset = strtoupper ( $matches [1] [0] );
			}
			else
			{
				// 考虑到网站可能没有在meta标题里规定编码，默认UTF8
				$this->charset = 'UTF-8';
			}
		}
	}
	
	public function getTitle($content)
	{
		$this->getMeta();
		// self::getContent($this->url);
		$pattern = '/<title>(.*)<\/title>/i';
		$matches = array ();
		if (! $content)
		{
			$this->title = "获取失败";
		}
		else
		{
			preg_match_all ( $pattern, $content, $matches );
			$this->title = iconv ( $this->charset, 'UTF-8', trim ( $matches [1] [0] ) );
		}
		
		$pattern_meta = '/<meta\s(.*?)=["\']?(.*?)["\']?\s(\w+)=["\']?(.*?)["\']?[\s]*[\/]?>/i';
		
		preg_match_all($pattern_meta, $content, $matches_meta);
		
		for ($i = 0; $i < count($matches_meta[0]); $i++)
		{
			$matches_meta[2][$i] = iconv ( $this->charset, 'UTF-8',trim($matches_meta[2][$i]));
			$matches_meta[4][$i] = iconv ( $this->charset, 'UTF-8',trim($matches_meta[4][$i]));
			if(strtolower($matches_meta[3][$i]) != "content")
			{
				$temp = $matches_meta[1][$i];
				$matches_meta[1][$i] = $matches_meta[3][$i];
				$matches_meta[3][$i] = $temp;
				
				$temp2 = $matches_meta[2][$i];
				$matches_meta[2][$i] = $matches_meta[4][$i];
				$matches_meta[4][$i] = $temp2;
			}
			$meta[strtolower($matches_meta[2][$i])] = $matches_meta[4][$i];
		}
		
		$this->keywords = isset($meta['keywords'])?$meta['keywords']:'';
		$this->description = isset($meta['description'])?$meta['description']:'';
	}
	
	public function getMeta()
	{
		$meta_arr = get_meta_tags($this->realurl);
		$this->keywords = isset($meta_arr['keywords'])?iconv($this->charset, 'UTF-8', $meta_arr['keywords']):'';
		$this->description = isset($meta_arr['description'])?iconv($this->charset, 'UTF-8', $meta_arr['description']):'';
	}
	
	private function __get($v)
	{
		return $this->$v;
	}
	
	private function getContent($url)
	{
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $this->timeout );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_USERAGENT, $this->agent );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // 是否抓取跳转后的页面
		
		$output = curl_exec ( $ch );

		if ($output === FALSE)
		{
			$this->error = curl_errno ( $ch ) . "|" . curl_error ( $ch );
			return false;
		}
		else
		{
			curl_close ( $ch );
			return $output;
		}
	}
	
	private function doURL()
	{
		if ($this->statuscode == 301)
		{
			if (strpos ( $this->url, 'http://www.' ))
			{
				$this->realurl = str_replace ( 'http://www.', '', $this->url );
			}
			else
			{
				$this->realurl = str_replace ( 'http://', 'http://www.', $this->url );
			}
		}
		else
		{
			$this->realurl = $this->url;
		}
	}
}