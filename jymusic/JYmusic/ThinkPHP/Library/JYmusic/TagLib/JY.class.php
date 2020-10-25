<?php
/** ***************************************
 * 版权所有 (C) 2012-2013 QQ:378020023	  *
 * ****************************************
 * $E-mail: 战神~~巴蒂 (378020023@qq.com) *
 * ***************************************/
namespace JYmusic\TagLib;
use Think\Template\TagLib;

/**
 +-------------------------------
 * JY标签库驱动(获取数据)所有必须至少带有属性，否则不解析
 +-------------------------------
 */
class JY extends TagLib {
	/*
	+----------------------------------------------------------
	*标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
	*标签属性：music -音乐数据，
	*$mod:输出记录的行数如$mod='2',输出偶数行记录
	+----------------------------------------------------------
	*/
	protected $tags   =  array( 
		// 标签定义： 定义标签中对应的属性
		'nav'       =>  array('attr' => 'cache_time,field,name', 'close' => 1), //获取导航
		'songs'=>array('attr'=>'result,id,artist_id,album_id,genre_id,up_uid,url,cache_time,pos,limit,order','level'=>3),
		'album'=>array('attr'=>'result,id,artist_id,type_id,genre_id,add_uid,url,cache_time,pos,limit,order','level'=>3),
		'artist'=>array('attr'=>'result,id,type_id,region_id,pos,sort,cache_time,url,limit,order','level'=>3),
		'tag'=>array('attr'=>'result,id,ids,cache_time,url,limit,order','level'=>3),
		'genre'=>array('attr'=>'result,id,ids,p_id,cache_time,limit,url,order','level'=>3),
		'dynamic'=>array('attr'=>'result,uid,limit','level'=>3),
		'member'=>array('attr'=>'result,id,cache_time,limit,order','level'=>3),
		'cate'      =>  array('attr'=>'id,name,limit,pid,result','level'=>3),
		'data'      =>  array('attr'=>'name,field,limit,order,where,join,group,having,table,result,gc','level'=>2),
		'article'   =>  array('attr'=>'id,name,cate,pid,pos,type,limit,where,order,field,result','level'=>3),	 			
	);        
    /**
     * music 歌曲标签解析 循环输出数据集
     */       
	public function _songs($tag,$content) {			
		$result		=   isset($tag['result'])?$tag['result']:'songs';
		$tag['url']	=   isset($tag['url'])?$tag['url']:'/Music/detail/id';
		$where = 'array("status"=>1';
		if(!empty($tag['up_uid'])) {
            $where .= ',"up_uid" =>'.$tag['up_uid'];
        }
        if(!empty($tag['artist_id'])) {
            $where .= ',"artist_id"=>'.$tag['artist_id'];
        }		
		if(!empty($tag['album_id'])){ //
            $where .= ',"album_id"=> '.$tag['album_id'];
        }
        if(!empty($tag['genre_id'])) {
            $where .= ',"genre_id"=>'.$tag['genre_id'];
        }
        if(!empty($tag['pos'])) {
			$pos = intval($tag['pos']);
            //$where .= ',"position"=>'.$tag['pos'];
			$where .= ',array("0"=>"position & '.$pos.' = '.$pos.'")';
        }
        $where .= ')';
        $field = '';
        $arr= array('name'=>'Songs','where'=>$where,'field'=>$field,'id'=>$result);
		return $this->_musiclist(array_merge($arr,$tag),$content);
    }
    /**
     * album 专辑标签解析 循环输出数据集
     */   
	public function _album($tag,$content) {
		$result		=   isset($tag['result'])?$tag['result']:'album';
		$tag['url']	=   isset($tag['url'])?$tag['url']:'/Album/detail/id';
		$where = 'array("status"=>1';
		if(!empty($tag['add_uid'])) {
            $where .= ',"add_uid"=>'.$tag['add_uid'];
        }
        if(!empty($tag['artist_id'])) {
            $where .= ',"artist_id"=>'.$tag['artist_id'];
        }		
		if(!empty($tag['type_id'])){ //
            $where .= ',"type_id=>'.$tag['type_id'];
        }
        if(!empty($tag['genre_id'])) {
            $where .= ',"genre_id"=>'.$tag['genre_id'];
        }
        if(!empty($tag['pos'])) {
			$pos = intval($tag['pos']);
            $where .= ',array("0"=>"position & {$pos} = {$pos}")';
        }
        $where .= ')';
        $field = '';
        $arr= array('name'=>'Album','where'=>$where,'field'=>$field,'id'=>$result);
		return $this->_musiclist(array_merge($arr,$tag),$content);
	}
	
    
	/**
	* artist 艺术家标签解析 循环输出数据集
	*/   
	public function _artist($tag,$content) {
		$result		=   isset($tag['result'])?$tag['result']:'artist';
		$tag['url']	=   isset($tag['url'])?$tag['url']:'/Artist/detail/id';
		$where = 'array("status"=>1';
		if(!empty($tag['type_id'])){ //
            $where .= ',"type_id"=>'.$tag['type_id'];
        }
        if(!empty($tag['region_id'])) {
            $where .= ',"region_id"=>'.$tag['region_id'];
        }
        if(!empty($tag['sort'])) {
            $where .= ',"sort"=>'.$tag['sort'];
        }        
        if(!empty($tag['pos'])) {
            $where .= ',"position"=>'.$tag['pos'];
        }
        $where .= ')';
		$field = '';
        $arr= array('name'=>'Artist','where'=>$where,'field'=>$field,'id'=>$result);
		return $this->_musiclist(array_merge($arr,$tag),$content);
	}
	/**
	* genre 曲风标签解析 循环输出数据集
	*/   
	public function _genre($tag,$content) {
		$result		=   isset($tag['result'])?$tag['result']:'genre';
		$tag['url']	=   isset($tag['url'])?$tag['url']:'/Genre/index/id';
		$where = 'array("status"=>1';
		if(!empty($tag['p_id'])){ //
            $where .= ',"p_id"=>'.$tag['p_id'];
        }
		if(!empty($tag['ids'])) {
			$where .= ',"id"=>array("in",array('.$tag['ids'].'))';
		}
        if(!empty($tag['pos'])) {
            $where .= ',"position"=>'.$tag['pos'];
        }
        $where .= ')';
        $field = '';
		$arr= array('name'=>'Genre','where'=>$where,'field'=>$field,'id'=>$result);
		return $this->_musiclist(array_merge($arr,$tag),$content);
    }
    
    /**
	* tag 标签解析 循环输出数据集
	*/   
	public function _tag($tag,$content) {
		$result		=   isset($tag['result'])?$tag['result']:'tag';
		$tag['url']	=   isset($tag['url'])?$tag['url']:'/Tag/detail/id';
		$where = 'array("status"=>1';
		if(!empty($tag['ids'])) {
			$where .= ',"id"=>array("in",array('.$tag['ids'].'))';
		}
		if(!empty($tag['id'])){
			$where .= ',"id"=>'.$tag['id'];
        }
        $where .= ')';
        $field = '';
		$arr= array('name'=>'Tag','where'=>$where,'field'=>$field,'id'=>$result);
		return $this->_musiclist(array_merge($arr,$tag),$content);
    }
    

    
    /**
	* member 会员标签解析 循环输出数据集
	*/   
	public function _member($tag,$content) {
		$result		=   isset($tag['result'])?$tag['result']:'member';
		$tag['url']	=   isset($tag['url'])?$tag['url']:'User/Home/index/uid';
		$tag['order'] 		= 	isset($tag['order'])? trim($tag['order']) : 'uid';
		$field = isset($tag['field'])?$tag['field']:'';
		$where = 'array("status"=>1,"uid"=>array("neq",1))';
		$field = '';
        $arr= array('name'=>'Member','where'=>$where,'field'=>$field,'id'=>$result);
		return $this->_musiclist(array_merge($arr,$tag),$content);
    }

	/**
     * video 视频标签解析 循环输出数据集
     */       
	public function _video($tag,$content) {			
		$result		=   isset($tag['result'])?$tag['result']:'video';
		$tag['url']	=   isset($tag['url'])?$tag['url']:'/video';
		$where = 'array("status"=>1';
		if(!empty($tag['uid'])) {
            $where .= ',"uid" =>'.$tag['uid'];
        }
		if(!empty($tag['type_id'])) {
            $where .= ',"type_id"=>'.$tag['type_id'];
        }
        $where .= ')';
        $field = '';
        $arr= array('name'=>'Video','where'=>$where,'field'=>$field,'id'=>$result);
		return $this->_musiclist(array_merge($arr,$tag),$content);
    }
	
	/* 共用列表 */
    public function _musiclist($tag, $content){
        $name       =   !empty($tag['name'])?$tag['name']:'songs';
        $result     =   !empty($tag['result'])?$tag['result']:'music';
        $key        =   !empty($tag['key'])?$tag['key']:'i';
        $mod        =   isset($tag['mod'])?$tag['mod']:'2';
        $order 		= 	isset($tag['order'])? trim($tag['order']) : 'id';
        $limit 		=   !empty($tag['limit'])?$tag['limit']:'10';
        $parseStr   =   '<?php $_result = M("'.$name.'")->alias("__MUSIC")';
        if(!empty($tag['table'])) {
            $parseStr .= '->table("'.$tag['table'].'")';
        }
		
        if(!empty($tag['where'])){
            $tag['where']=$this->parseCondition($tag['where']);
            $parseStr .= '->where('.$tag['where'].')';
        }
		
		if(!empty($tag['page'])){
			$listrow = !empty($tag["limit"]) ? $tag["limit"] : 20;
			$parseStr .= '->page(!empty($_GET["p"])?$_GET["p"]:1,'.$listrow.')';
		}else{		
			if(!empty($tag['cache_time'])) {
				$cacheTime  = intval($tag['cache_time']);
				if($cacheTime){
					$parseStr .= '->cache(true,"'.$cacheTime.'")';  
				}
			}else{
				$parseStr .= '->cache(true,intval(C("LABEL_CACHE_TIME")))';
			}
			if(!empty($tag['limit'])){
				$parseStr .= '->limit("'.$tag['limit'].'")';
			}
		}			
		
		if(!empty($tag['order'])){
			if (stristr($tag['order'],',')){
				$order = strtr($tag['order'],array(','=>' desc,')).' desc';
			}else{
				$order = $tag['order'].' desc';
			}			
            $parseStr .= '->order("'.$order.'")';
        }
        
        if(!empty($tag['field'])){
            $parseStr .= '->field("'.$tag['field'].'")';
        }
				
        $parseStr .= '->select();if($_result):$'.$key.'=0;foreach($_result as $key=>$'.$result.'): ';
        if(!empty($tag['url'])){     
	        if ($name != 'Member'){       
	        	$parseStr .= '$'.$result.'[\'url\']=U(\''.$tag['url'].'/\'.$'.$result.'[\'id\']);';
	        }else {       
	        	$parseStr .= '$'.$result.'[\'url\']=U(\''.$tag['url'].'/\'.$'.$result.'[\'uid\']);';
	        }
    	}
        $parseStr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>'.$content;
        $parseStr .= '<?php endforeach; endif;?>';
		if (!empty($tag['page'])){
			$parseStr  .= '<?php ';
			$parseStr .= '$total= M("'. $name.'")->where('.$tag['where'] .')->count();';
			$parseStr  .= '$__PAGE__ = new \Think\Page($total,' . $listrow . ');';
			$parseStr  .= '$__PAGE__->setConfig("theme","%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%");';            
			$parseStr  .='$__PAGE__->setConfig("prev", "上页");';
			$parseStr .='$__PAGE__->setConfig("next", "下页");';
			$parseStr  .= '$'.$result.'_page= $__PAGE__->show();';
			$parseStr  .= ' ?>';				
		}		
        return $parseStr;
    }
    
       /**
	* dynamic 用户最新动态标签解析 循环输出数据集
	*  获取数据不完整
	*  下一版本完善
	*/   
	public function _dynamic($tag,$content) {
		$result		=   isset($tag['result'])?$tag['result']:'dynamic';	
		$uid		=   isset($tag['uid'])?$tag['uid']:0;
		$limit	=   isset($tag['limit'])?$tag['limit']:10;
		$mod        =   isset($tag['mod'])?$tag['mod']:'2';
		$key        =   !empty($tag['key'])?$tag['key']:'i';
        $parseStr   =   '<?php $_result = get_user_dynamic('.$uid.',null);';
        $parseStr  .=   '$_result = array_slice($_result,0,'.$limit.');';
        $parseStr .= 'if($_result):$'.$key.'=0; foreach($_result as $key=>$'.$result.'): ';
        $parseStr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>'.$content;
        $parseStr .= '<?php endforeach; endif;?>';
        return $parseStr;
    }	
	
	
	
	/* 列表数据分页 */
	public function _page($tag){
		$table   = $tag['table'];
		$map    = $tag['map'];
		$listrow = $tag['listrow'];
		$parse   = '<?php ';
		$parse  .= '$__PAGE__ = new \Think\Page(music_list_count("' . $table.'",'.$map  . '), ' . $listrow . ');';
		$parse  .= '$__PAGE__->setConfig("theme","%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%");';            
		$parse  .='$__PAGE__->setConfig("prev", "上页");';
        $parse  .='$__PAGE__->setConfig("next", "下页");';
		$parse  .= 'echo $__PAGE__->show();';
		$parse  .= ' ?>';
		return $parse;
	}
    
    /* 导航列表 */
    public function _nav($tag, $content){
        $field  = empty($tag['field']) ? 'true' : $tag['field'];
		$cacheTime  = empty($tag['cache_time']) ? 86400 : intval($tag['cache_time']);
        $tree   =   empty($tag['tree'])? false : true;
        $parse  = $parse   = '<?php ';
        $parse .= '$__NAV__ = M(\'Channel\')->field('.$field.')->where("status=1")->cache(true,"'.$cacheTime.'")->order("sort")->select();';
        if($tree){
            $parse .= '$__NAV__ = list_to_tree($__NAV__, "id", "pid", "_child");';
        }
        $parse .= '?><volist name="__NAV__" id="'. $tag['name'] .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    } 
    
	public function _data($tag,$content){
        $name       =   !empty($tag['name'])?$tag['name']:'Document';
        $result     =   !empty($tag['result'])?$tag['result']:'article';
        $parseStr   =   '<?php $'.$result.' =M("'.$name.'")->alias("__MUSIC")';
        if(!empty($tag['table'])) {
            $parseStr .= '->table("'.$tag['table'].'")';
        }
        if(!empty($tag['where'])){
            $tag['where']=$this->parseCondition($tag['where']);
            $parseStr .= '->where("'.$tag['where'].'")';
        }
        if(!empty($tag['order'])){
            $parseStr .= '->order("'.$tag['order'].'")';
        }
        if(!empty($tag['join'])){
            $parseStr .= '->join("'.$tag['join'].'")';
        }
        if(!empty($tag['group'])){
            $parseStr .= '->group("'.$tag['group'].'")';
        }
        if(!empty($tag['having'])){
            $parseStr .= '->having("'.$tag['having'].'")';
        }
        if(!empty($tag['field'])){
            $parseStr .= '->field("'.$tag['field'].'")';
        }
        $parseStr .= '->find();?>'.$content;
        if(!empty($tag['gc'])) {
            $parseStr .= '<?php unset($'.$result.');?>';
        }
        return $parseStr;
    }
    
	public function _article($tag,$content){
        $result      =  !empty($tag['result'])?$tag['result']:'article';
        $name	=	!empty($tag['name'])?$tag['name']:'Article';
        $order   =  empty($tag['order'])?'level,create_time':$tag['order'];
        $field  =   empty($tag['field'])?'*':$tag['field'];
        $tag['url']	=   isset($tag['url'])?$tag['url']:'Article/detail/?id=';
        $join   =   'INNER JOIN __DOCUMENT_'.strtoupper($name).'__ ON __DOCUMENT.id = __DOCUMENT_'.strtoupper($name).'__.id';
        if(!empty($tag['id'])) { // 获取单个数据
            return $this->_data(array('name'=>"Document", 'where'=>'status=1 AND __DOCUMENT.id='.$tag['id'], 'field'=>$field,'result'=>$result,'order'=>$order,'join'=>$join),$content);
        }else{ // 获取数据集
            $where = 'array("status"=>1';            
            if(!empty($tag['model'])) {
                $where .= ' AND model_id='.$tag['model'];
            }
            if(!empty($tag['cate'])) { // 获取某个分类的文章
                if(strpos($tag['cate'],',')) {
                    $where .= ' AND category_id IN ('.$tag['cate'].')';
                }else{
                    $where .= ' AND category_id='.$tag['cate'];
                }
            }
            if(!empty($tag['pid'])){ //
                $where .= ',pid => '.$tag['pid'];
            }
            if(!empty($tag['pos'])) {
                $where .= ',position =>'.$tag['pos'];
            }
            if(!empty($tag['where'])) {
                $where  .=  ' AND '.$tag['where'];
            }
            $where .= ')';
            return $this->_musiclist(array('name'=>'Document','where'=>$where,'field'=>$field,'result'=>$result,'order'=>$order,'join'=>$join,'limit'=>!empty($tag['limit'])?$tag['limit']:''),$content);
        }
    }
    
    
	// 获取分类信息
    public function _cate($tag,$content){
        $result      =  !empty($tag['result'])?$tag['result']:'cate';
        if(!empty($tag['id'])) {
            // 获取单个分类
            $parseStr   =  '<?php $'.$result.' = M("Category")->find('.$tag['id'].');';
            $parseStr .=  'if($'.$result.'):?>'.$content;
        }elseif(!empty($tag['name'])) {
            // 获取单个分类
            $parseStr   =  '<?php $'.$result.' = M("Category")->getByName('.$tag['name'].');';
            $parseStr .=  'if($'.$result.'):?>'.$content;
        }elseif(!empty($tag['pid']) || $tag['pid'] == '0'){
            $key     =   !empty($tag['key'])?$tag['key']:'i';
            $mod    =   isset($tag['mod'])?$tag['mod']:'2';
            $parseStr   =  '<?php $_result = M("Category")->order("sort")->where("display=1 AND status=1 AND pid='.$tag['pid'].'")';
            if(!empty($tag['limit'])){
                $parseStr .= '->limit('.$tag['limit'].')';
            }
            $parseStr .= '->select();';
            $parseStr  .=  'if($_result):$'.$key.'=0;foreach($_result as $key=>$'.$result.'): ';
            $parseStr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );';
            $parseStr .=  'if($'.$result.'):?>'.$content.'<?php endif; endforeach;?>';
        }
        $parseStr .= "<?php endif;?>";
        return $parseStr;
    }
}
