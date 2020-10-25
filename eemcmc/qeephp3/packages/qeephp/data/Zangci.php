<?php namespace qeephp\data;

class Zangci
{
	private $polluted_word;
	private $dict;
	private $result;
	private $replace_chars = array( '＠' => '@');
	
	/**
	 * @return \qeephp\data\Zangci;
	 */
	static public function getInstance()
	{
		static $inst = null;
		if (empty($inst)){
			$inst = new self();
			$inst->loadDict();
		} 
		return $inst;
	}
	
	private function __construct()
	{
		$this->dict = $this->loadDict();
	}	
	
	/*
	 **获取关键字列表
	 */
	private function getKeyWordList(){
		$keyword_file = __DIR__.'/zangci.txt';
		$keywords = file_get_contents($keyword_file);
		//return array_unique(explode("\n",$keywords));
		$keywords_array = array_unique(preg_split("/[\r\n]/",$keywords));
		foreach ($keywords_array as $index => $key) {
			if(iconv_strlen(trim($key),'UTF-8')==0)
			unset ($keywords_array[$index]);
		}
		return $keywords_array;
	}
		
	public function getDict(){
		return $this->dict;
	}
	
	/**
	 * 获取匹配的脏词
	 * 
	 * @return string
	 */
	public function getPollutedWord(){
		return $this->polluted_word;
	}
	
	private function loadDict(){
		$keywords=$this->getKeyWordList();
		$dict=array();
		foreach($keywords as $keyword){
			if(empty($keyword)){
				continue;
			}
			$key = iconv_substr($keyword,0,2,'UTF-8');
			$dict[$key]['list'][]=$keyword;
			if(!isset($dict[$key]['max']))
			$dict[$key]['max'] = 0;
			$dict[$key]['max']=max($dict[$key]['max'],iconv_strlen($keyword,'UTF-8'));
		}
		return $dict;
	}

	/**
	 * 检查文本中是否存在敏感词,是返回false
	 * 
	 * @param string $content
	 * 
	 * @return bool
	 */
	public function isPolluted($resource){
		$len = iconv_strlen($resource,'UTF-8');
		for($i=0; $i<$len; ++$i){
			$key=iconv_substr($resource,$i,2,'UTF-8');
			if(array_key_exists($key,$this->dict)){
				if($this->deal(iconv_substr($resource,$i,$this->dict[$key]['max'],'UTF-8'),$key,$af))
				{
					return TRUE;	
				}				
				$i+=$af;
			}
			else{
				$this->result .=iconv_substr($resource,$i,1,'UTF-8');
			}
		}
		return FALSE;
	}

	/*
	 *匹配到了关键字时的处理
	 *$res 源字符串
	 *$keywords　关键字数组
	 */
	private function deal($res,$key,&$af){
		$af=0;
		foreach($this->dict[$key]['list'] as $keyword){
			
			if(iconv_strpos($res,$keyword,0,'UTF-8') !==false){
				$this->polluted = $keyword;
				$len=iconv_strlen($keyword,'UTF-8');
				$af=$len-1;
				$this->result .=str_repeat("*",$len);
				return TRUE;
			}
		}
		$this->result .= iconv_substr($res,0,1,'UTF-8');
		return FALSE;
	}
	
	/**
	 * 将文本中的敏感词替换成一个*号，并替换指定的符号
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */
	public function purify($resource){
		
		$len = iconv_strlen($resource,'UTF-8');
		for($i=0; $i<$len; ++$i){
			$key=iconv_substr($resource,$i,2,'UTF-8');
			if(array_key_exists($key,$this->dict)){
				$this->deal(iconv_substr($resource,$i,$this->dict[$key]['max'],'UTF-8'),$key,$af);
				$i+=$af;
			}
			else{
				$this->result .=iconv_substr($resource,$i,1,'UTF-8');
			}
		}
		foreach ( $this->replace_chars as $char => $replace ) {
			$this->result = preg_replace( "/" . $char . "/i", $replace, $this->result);
		}
		return $this->result;
	}
}