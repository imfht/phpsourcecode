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
 * music标签库驱动(获取数据)所有必须至少带有属性，否则不解析
 +-------------------------------
 */
class Gq extends TagLib {
	/*
	+----------------------------------------------------------
	*标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
	*标签属性：music -音乐数据，
	*$mod:输出记录的行数如$mod='2',输出偶数行记录
	+----------------------------------------------------------
	*/
	protected $tags   =  array( 
		// 标签定义： 定义标签中对应的属性
		'music'=>array('attr'=>'name,id,artist_id,album_id,genre_id,type_id,p_id,pos,limit,order,sort,offset,length,mod','level'=>3),
		'page'=> array('attr' => 'table,map,listrow', 'close' => 0), //列表分页
		'nav'       =>  array('attr' => 'field,name', 'close' => 1) //获取导航
	); 

       
    /**
     * music标签解析 循环输出数据集
     * 格式：
     * <music name="songs" empty="" >
     * {$name}
     * {$id}
     * </music>
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
     
     public function _music($tag,$content) {
        $name     	=   ucfirst($tag['name']);
		$id			=   isset($tag['id'])?$tag['id']:$tag['name'];
        $key     	=   !empty($tag['key'])?$tag['key']:'i'; 
		$limit 		= 	isset($tag['limit'])?$tag['limit']:'10';//设置显示条数	
		//$order 		= 	isset($tag['order'])?(isset($tag['sort']) ? $tag['order'].' '.$tag['sort'] : $tag['order'].' desc'):'';//排序方式	 
		$sid		= 	isset($tag['artist_id'])?'\'artist_id\'=>'.$tag['artist_id'].',':'';//所属歌手id的数据
		$aid 		= 	isset($tag['album_id'])?'\'album_id\'=>'.$tag['album_id'].',':'';//所属专辑id的数据
		$gid 		= 	isset($tag['genre_id'])?'\'genre_id\'=>'.$tag['genre_id'].',':'';//所属曲风id的数据
		$tid 		= 	isset($tag['type_id'])?'\'type_id\'=>'.$tag['type_id'].',':'';//所属曲风id的数据
		$pid 		= 	isset($tag['p_id'])?'\'pid\'=>'.$tag['p_id'].',':'';//所属父id的数据
		$pos 		= 	isset($tag['pos'])?'\'position\'=>'.$tag['pos'].',':'';//推荐位
		$order 		= 	isset($tag['order'])? trim($tag['order']) : '';
		if (!empty($order)){	
			if (stristr($order,',')){
				$order = strtr($order,array(','=>' desc,')).' desc';
			}else{
				$order = $order.' desc';
			}			
		}else{
			$order = 'id desc';
		}
		
		$where      = "array(";
		if (!empty($sid)|| !empty($aid) || !empty($gid) || !empty($tid) || !empty($pid) || !empty($pos)){//判断查询条件id
				$ids =  $sid.$aid.$gid.$tid.$pid.$pos;	
				$where .=substr($ids,0,-1);
				$where .=")";			
		}else{
			$where = "''";	
		}
		$mod   		=   isset($tag['mod'])?$tag['mod']:'2';
		//$arrStr 	= 	$this->getArrStr('',$order,$limit);
        $parseStr   =   "<?php \$_".$name."= getResult('".$name."',".$where.",'".$order."','".$limit."');"; 
        $parseStr   .= 	"if(is_array(\$_".$name.")):  $".$key." = 0;";
       	if(isset($tag['length']) && '' !=$tag['length'] ) {
			$parseStr  .= ' $_result = array_slice($_'.$name.','.$tag['offset'].','.$tag['length'].',true);';
		}elseif(isset($tag['offset'])  && '' !=$tag['offset']){
            $parseStr  .= ' $_result = array_slice($_'.$name.','.$tag['offset'].',null,true);';
        }else{
            $parseStr .= ' $_result = $_'.$name.';';
        }
        $parseStr   .= 	"foreach(\$_result as $".$key."=>\$".$id."):";
        //$parseStr   .= 	'extract($'.$name.');';
        if ('Songs' == $name) {  
			$name = 'Play/id';
		}elseif ('AlbumType' == $name)  {
			$name = 'Album/type/id';
		}elseif ('ArtistType' == $name)  {
			$name = 'Artist/type/id';
		}elseif('Album' == $name){
			$name = 'Album/detail/id';
		}elseif('Artist' == $name){
			$name = 'Artist/detail/id';
		}
        $parseStr   .= 	'$'.$id.'[\'url\']=U(\''.$name.'/\'.$'.$id.'[\'id\']);';
        $parseStr   .= 	' $mod = ($'.$key.' % '.$mod.' ); ++$'.$key.';?>';
        $parseStr   .=  $this->tpl->parse($content);
        $parseStr   .=  '<?php endforeach; endif; ?>';
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
        $tree   =   empty($tag['tree'])? false : true;
        $parse  = $parse   = '<?php ';
        $parse .= '$__NAV__ = M(\'Channel\')->field('.$field.')->where("status=1")->order("sort")->select();';
        if($tree){
            $parse .= '$__NAV__ = list_to_tree($__NAV__, "id", "pid", "_");';
        }
        $parse .= '?><volist name="__NAV__" id="'. $tag['name'] .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }
}
