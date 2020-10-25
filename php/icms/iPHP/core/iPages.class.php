<?php
/**
 * iPHP - i PHP Framework
 * Copyright (c) iiiPHP.com. All rights reserved.
 *
 * @author iPHPDev <master@iiiphp.com>
 * @website http://www.iiiphp.com
 * @license http://www.iiiphp.com/license
 * @version 2.1.0
 */
//iPages::$setting['url']="/index_";
//iPages::$setting['enable']=true;
class iPages {
	public $page_sign  = '{P}';
	public $page_name  = "page";//page标签，用来控制url页。比如说xxx.php?page=2中的page
	public $page_nav   = "NAV";
	public $page_style = 0;
	public $page_item  = '';
	public $is_ajax    = false;//是否支持AJAX分页模式
	public $ajax_fun   = null;   //AJAX动作名
	public $titles     = array();
	public $target     = '_self';

	public $barnum    = 8;//控制记录条的个数。
	public $totalpage = 0;//总页数
	public $nowindex  = 1;//当前页
	public $url       = null;//url地址头
	public $offset    = 0;
	public $lang      = array('index'=>'INDEX','prev'=>'PREV','next'=>'NEXT','last'=>'LAST','other'=>'Total','unit'=>'Page','list'=>'Articles','sql'=>'Records','tag'=>'Tags','comment'=>'Comments','message'=>'Messages');
	public $class     = array('index'=>'INDEX','prev'=>'PREV','next'=>'NEXT','last'=>'LAST','other'=>'Total','unit'=>'Page','list'=>'Articles','sql'=>'Records','tag'=>'Tags','comment'=>'Comments','message'=>'Messages');

	public static $config = array();
	public static $setting = array();
	public static $instance;

	/**
	* constructor构造函数
	*
	* @param array $array['total'],$array['perpage'],$array['pn'],$array['unit'],$array['nowindex'],$array['url'],$array['ajax'],$array['pnName']...
	*/
	public function __construct($conf){
 		array_key_exists('total',$conf) OR trigger_error('need a param of total(1001)');
 		self::$config 	 = $conf;
		$this->total     = (int)$conf['total'];
		$this->perpage   = $conf['perpage']?(int)$conf['perpage']:10;
		$this->totalpage = ceil($this->total/$this->perpage);

		if($this->totalpage<1) return false;

		$url = self::$setting['url']?self::$setting['url']:$_SERVER['REQUEST_URI'];
		isset($conf['url']) && $url = $conf['url'];
		self::$config['url'] = $url;

		//配置
		$conf['pnstyle']  && $this->page_style = $conf['pnstyle'];
		$conf['pagenav']  && $this->page_nav   = strtoupper($conf['pagenav']);
		$conf['page_name']&& $this->page_name  = $conf['page_name'];
		$conf['item']     && $this->page_item  = $conf['item'];
		$conf['barnum']   && $this->barnum     = $conf['barnum'];
		$conf['target']   && $this->target     = $conf['target'];
		$conf['titles']   && $this->titles     = $conf['titles'];
		$conf['lang']     && $this->lang       = $conf['lang'];
		$conf['class']    && $this->class      = $conf['class'];

		$this->unit = $conf['unit']?$conf['unit']:$this->lang['sql'];
		//设置当前页
		$nowindex = isset($conf['nowindex'])?(int)$conf['nowindex']:0;
		$this->_set_nowindex($nowindex);
		//设置链接地址
		$this->_set_url($url,$conf['total_type']);
		// $this->nowindex = min($this->totalpage,$this->nowindex);
		$this->offset = (int)($this->nowindex-1<0?0:$this->nowindex-1)*$this->perpage;

		//打开AJAX模式
		$conf['ajax'] && $this->ajax($conf['ajax']);
	}

	/**
	* 打开倒AJAX模式
	*
	* @param string $action 默认ajax触发的动作。
	*/
	public function ajax($action){
		$this->is_ajax  = true;
		$this->ajax_fun = $action;
	}

	public function vars(){
		return array(
	        $this->page_nav  => ($this->totalpage > 1)?$this->show($this->page_style):'',
	        'COUNT'   => $this->total,
	        'TOTAL'   => $this->totalpage,
	        'CURRENT' => $this->nowindex,
	        'PN'      => $this->nowindex,
	        'PREV'    => $this->prev_page('array'),
	        'NEXT'    => $this->next_page('array'),
	        'LAST'    => ($this->nowindex>=$this->totalpage),
        );
	}

	/**
	* 获取显示"下一页"的代码
	*
	* @param string $style
	* @return string
	*/
	public function next_page($style='page_next'){
		$p = $this->nowindex+1;
		if($p>$this->totalpage){
			$p = $this->totalpage;
		}
		$pnt = $this->get_title($p,$this->lang['next']);
		return $this->_get_link($p,$pnt,$style,($this->nowindex<$this->totalpage));
	}

	/**
	* 获取显示“上一页”的代码
	*
	* @param string $style
	* @return string
	*/
	public function prev_page($style='page_prev'){
		$p = $this->nowindex-1;
		$p<2 && $p = 1;
		$pnt = $this->get_title($p,$this->lang['prev']);
		return $this->_get_link($p,$pnt,$style,($this->nowindex>1));
	}

	/**
	* 获取显示“首页”的代码
	*
	* @return string
	*/
	public function first_page($style='page_index'){
		$pnt = $this->get_title(1,$this->lang['index']);
		return $this->_get_link(1,$pnt,$style,true);
	}

	/**
	* 获取显示“尾页”的代码
	*
	* @return string
	*/
	public function last_page($style='page_last'){
		$pnt = $this->get_title($this->totalpage,$this->lang['last']);
		return $this->_get_link($this->totalpage,$pnt,$style,true);
	}
	public function last_text($style='page_last'){
		$text = $this->lang['other'].$this->totalpage.$this->lang['unit'];
		$pnt  = $this->get_title($this->totalpage,$text);
		return $this->_get_link($this->totalpage,$pnt,$style,true);
	}
	public function current_page($style='page_nowindex'){
		$pnt = $this->get_title($this->nowindex);
		$title = $this->titles?$pnt:($this->lang['di'].$pnt.$this->lang['unit']);
		return '<span class="'.$style.'">'.$title.'</span>';
	}
	//文字 说明
	public function bartext($style='bartext'){
		return '<span class="'.$style.'">'.$this->total.$this->unit.'，'.$this->lang['other'].$this->totalpage.$this->lang['unit'].'</span>';
//		return '<span class="'.$style.'">'.$this->lang['other'].$this->total.$this->unit.'，'.$this->perpage.$this->unit.'/'.$this->lang['unit'].' '.$this->lang['other'].$this->totalpage.$this->lang['unit'].'</span>';
	}
	public function nowbar($style='',$nowindex_style='page_nowindex'){
		$plus   = ceil($this->barnum/2);
		$before = $this->nowindex-$plus;
		$after  = $this->nowindex+$plus-1;
		if($before<1){
			$after  = $this->barnum;
			$before = 1;
		}
		if($after>$this->totalpage){
			$after = $this->totalpage;
			$before = $this->totalpage-$this->barnum;
			$before<1 && $before = 1;
		}

		for ($i=$before; $i <=$after; $i++) {
			$pnt = $this->get_title($i);
			$now = ($i!=$this->nowindex);
			if($now){
				$pieces[] = $this->_get_link($i,$pnt,$style,$now);
			}else{
				$pieces[] =$this->_get_text('<span class="'.$nowindex_style.'">'.$pnt.'</span>');
			}
		}
		return implode('', $pieces);
	}
	public function list_page(){
		$pieces = array();
		for($i=1;$i<=$this->totalpage;$i++){
			$pnt      = $this->get_title($i);
			$pieces[] = $this->_get_link($i,$pnt,'array',true);
		}
		return $pieces;
	}
	/**
	* 获取显示跳转按钮的代码
	*
	* @return string
	*/
	public function select($style='page_select'){
		$return='<select class="'.$style.'" name="Page_Select" onchange="window.location.href=this.value">';
		for($i=1;$i<=$this->totalpage;$i++){
			$url = $this->get_url($i);
			$pnt = $this->get_title($i);
			if($i==$this->nowindex){
				$return.='<option value="'.$url.'" selected>'.$pnt.'</option>';
			}else{
				$return.='<option value="'.$url.'">'.$pnt.'</option>';
			}
		}
		$return.='</select>';
		return $return;
	}
	public function select_wrap($style='page_select_wrap'){
		return $this->_get_text('<span class="'.$style.'">'.
		$this->lang['di'].
		$this->select().
		$this->lang['unit'].
		'</span>');
	}
	/**
	* 获取mysql 语句中limit需要的值
	*
	* @return string
	*/
	public function offset(){
		return $this->offset;
	}

	/**
	* 控制分页显示风格（你可以增加相应的风格）
	*
	* @param int $mode
	* @return string
	*/
	public function show($mode=0){
		if($this->totalpage<2){
			return '';
		}
		switch ($mode){
			case '1':
				return $this->prev_page().$this->nowbar().$this->next_page().$this->select_wrap();
				break;
			case '2':
				return $this->first_page().$this->prev_page().$this->nowbar().$this->next_page().$this->last_page().$this->select_wrap();
				break;
			case '3':
				return $this->first_page().$this->prev_page().$this->nowbar().$this->next_page().$this->last_page();
				break;
			case '4':
				return $this->prev_page().$this->nowbar().$this->next_page();
				break;
			case '5':
				return $this->nowbar();
				break;
			case '6':
				return $this->prev_page().$this->next_page();
				break;
			case '7':
				return $this->first_page().$this->prev_page().$this->current_page().$this->next_page().$this->last_page().$this->bartext();
				break;
			case '8':
				return $this->first_page().$this->prev_page().$this->current_page().$this->next_page().$this->last_page();
				break;
			case '9':
				return $this->first_page().$this->prev_page().$this->next_page().$this->last_page();
				break;
			case '10':
				return $this->first_page().$this->prev_page().$this->current_page().$this->next_page().$this->last_text();
				break;
			default:
				return $this->first_page().$this->prev_page().$this->nowbar().$this->next_page().$this->last_text();
				break;
		}
	}
/*----------------private function (私有方法)-----------------------------------------------------------*/
	/**
	* 设置url头地址
	* @param: String $url
	* @return boolean
	*/
	public function _set_url($url="",$total_type=null){
		if(self::$setting['enable']){
			$this->url	= $url;
		}else{
			$query = array();
			$total_type ==="G" && $query['total_num'] = $this->total;
			$query[$this->page_name] ="---PN---";
			$this->url = iURL::make($query,$url);
			$this->url = str_replace('---PN---',$this->page_sign,$this->url);
		}
	}

	/**
	* 设置当前页面
	*
	*/
	public function _set_nowindex($pn){
		if(empty($pn)){ //系统获取
			if(isset($_GET[$this->page_name])){
				$pn = $_GET[$this->page_name];
			}
		}
		$this->nowindex=intval($pn);
		if($this->nowindex>$this->totalpage){
			$this->nowindex = $this->totalpage;
		}
        $this->nowindex<1 && $this->nowindex =1;
	}
    public function get_title($pn=0,$text=null){
        $title = $pn;
        $text && $title = $text;
        $this->titles && $title = $this->titles[$pn];
        return $title;
    }

	/**
	* 为指定的页面返回地址值
	*
	* @param int $pageno
	* @return string $url
	*/
	public function get_url($pageno=1){
		if($this->is_ajax) return (int)$pageno;
		if($pageno<2){
			if(self::$setting['index']){
				return self::$setting['index'];
			}
			$url = $this->url;
			self::$setting['enable'] OR $url = str_replace(array('?'.$this->page_name.'='.$this->page_sign,'&'.$this->page_name.'='.$this->page_sign),'',$this->url);
			$url = preg_replace('@&total_num=\d+@is', '', $url);
			return str_replace(array('_'.$this->page_sign,$this->page_sign),array('',1),$url);
		}
		return str_replace($this->page_sign,$pageno,$this->url);
	}

	/**
	* 获取分页显示文字，比如说默认情况下_get_text('<a href="">1</a>')将返回[<a href="">1</a>]
	*
	* @param String <li>%s</ii>
	* @return string $url
	*/
	public function _get_text($text){
		// if($this->page_item){
		// 	if(strpos($this->page_item, '%s')!==false){
		// 		$text = sprintf($this->page_item,$text);
		// 	}else{
		// 		$text = '<'.$this->page_item.'>'.$text.'</'.$this->page_item.'>';
		// 	}
		// }
		return $this->page_item?sprintf($this->page_item,$text):$text;
	}


	/**
	* 获取链接地址
	*/
	public function _get_link($i,$text,$style='',$flag=true){

		if($style=='array'){
			return $this->_get_array($i,$text);
		}
		if($style){
			$this->class[$style] && $style = $this->class[$style];
			$class	= ' class="'.$style.'"';
		}

		if(!$flag){
			return $this->_get_text('<span'.$class.'>'.$text.'</span>');
		}

		$url = $this->get_url($i);
		if($this->is_ajax){
	  		//如果是使用AJAX模式
	  		$a = '<a'.$class.' href="javascript:;" onclick="'.$this->ajax_fun.'(\''.$url.'\',this)">'.$text.'</a>';
		}else{
			$a = '<a'.$class.' href="'.$url.'" target="'.$this->target.'">'.$text.'</a>';
		}
		return $this->_get_text($a);
	}
	public function _get_array($i,$text){
		return array(
			'pn'    => $i,
			'url'   => $this->get_url($i),
			'title' => $text,
			'link'  => $this->_get_link($i,$text),
		);
	}
}
