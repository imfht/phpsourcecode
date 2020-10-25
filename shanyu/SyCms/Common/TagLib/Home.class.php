<?php
namespace Common\TagLib;
use Think\Template\TagLib;

//自定义标签库
class Home extends TagLib {
	
	protected $tags = array(
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		
		//自定义标签
		//artlist 获取指定栏目id下的信息列表
		//channel 获取栏目列表
		//category 获取单个栏目信息
		//flink 获取友情链接
		//hotwords 获取搜索热门关键词
		

		//获取指定栏目cid下的栏目列表
		'channel'	=> array(
			'attr'	=> 'cid,limit',
			'close'	=> 1,
			'level'=>1
		),
		//获取指定栏目cid的栏目信息
		'category'	=> array(
			'attr'	=> 'cid',
			'close'	=> 1,
		),
		//获取指定栏目cid下的信息列表
		'artlist'	=> array(
			'attr'	=> 'cid,limit,order,page',
			'close'	=> 1,
			'level'=>1
		),
		//获取指定数据表的信息列表
		'tablist'	=> array(
			'attr'	=> 'table,limit,order',
			'close'	=> 1,
			'level'=>1
		),
		//获取当前位置
		'position'	=> array(
			'attr'	=> 'cid,mark',
			'close'	=> 0,
		),
		//自定义导航
		'nav'	=> array(
			'attr'	=> 'groups,limit',
			'close'	=> 1,
		),
		//数据块
		'data'	=> array(
			'attr'	=> 'name',
			'close'	=> 0,
		),

	);
	//获取指定cid下的栏目树形列表
	public function _channel($attr, $content) {
		$cid = empty($attr['cid']) ? 0 : $attr['cid'];
		if('$' == substr($cid,0,1)){
			$cid_var=substr($cid,1);
			$cid=$this->tpl->tVar[$cid_var];
		}
		$limit = empty($attr['limit'])? '8' : intval($attr['limit']) -1;

		$str = <<<str
<?php
	if(!isset(\$categorys)){
		\$categorys=D('Common/Category')->getCache();
	}
	
	if(!isset(\$_channel_{$cid})){
		\$_channel_{$cid}=\Lib\ArrayTree::listTree(\$categorys,$cid,'id','pid','_child');
	}

    foreach (\$_channel_{$cid} as \$k => \$v){
    	if(\$k > $limit) break;
?>
$content
<?php
}
?>
str;
		return $str;
	}
	//获取指定cid的单个栏目信息
	public function _category($attr, $content){
		$cid = empty($attr['cid']) ? '' : $attr['cid'];

		$str=<<<str
<?php
	if(!isset(\$categorys)){
		\$categorys=D('Common/Category')->getCache();
	}
	\$v=\$categorys[$cid];
?>
$content
str;
		return $str;
	}

	//获取指定cid下的信息列表
	public function _artlist($attr, $content) {
		$cid = empty($attr['cid']) ? '' : $attr['cid'];	
		$order = empty($attr['order'])? 'id DESC' : $attr['order'];
		$page = empty($attr['page']) ? 0 : intval($attr['page']);
		$limit = empty($attr['limit'])? 6 : trim($attr['limit']);
		$router= intval(C('URL_ROUTER_ON'));
$str = <<<str
<?php
if(!isset(\$categorys)):
	\$categorys=D('Common/Category')->getCache();
endif;
\$cate=isset(\$categorys[$cid]) ? \$categorys[$cid] : array();
if(!empty(\$cate)):
	if(\$cate['is_menu']):
		foreach (\$categorys as \$k => \$v) {
			if(\$v['pid'] == $cid) \$cid_arr[]=\$v['id'];
		}
		if(!empty(\$cid_arr)){
			\$map=array('cid'=>array('IN',\$cid_arr));
		}else{
			E(\$cate['title'].' 无子栏目');
		}
	else:
	    \$map=array('cid'=>$cid);
	endif;

	\$table=ucfirst(\$cate['table']);
	\$p=I('p',0,'intval');

	if($page):
		\$count=M(\$table)->where(\$map)->count();
	    \$Page = new \Lib\Page(\$count,$limit);
	    if($router):\$Page->url = \$cate['name'];endif;
	    \$page = \$Page->show();	
	endif;
	\$_artlist=M("\$table t")
		->join("LEFT JOIN __CATEGORY__ c ON t.cid=c.id")
	    ->field("t.*,c.title as cate_title,c.name as cate_name")
	    ->where(\$map)
	    ->order("t.$order")
	    ->limit("\$p,$limit")
	    ->select();
	if(!empty(\$_artlist)):
	foreach (\$_artlist as \$k => \$v):
		if($router): \$v['url']=U('/'.\$v['cate_name'].'/'.\$v['id'],'','html',true);
		else: \$v['url']=U('Show/index',array('cid'=>\$v['cid'],'id'=>\$v['id']),'html',true);
		endif;
	?>
	$content
	<?php
	endforeach;
	else:
		echo '暂时没有查询到数据';
	endif;
endif;
	?>
str;
		return $str;
	}

	public function _tablist($attr, $content){
		$table = empty($attr['table']) ? '' : $attr['table'];	
		$order = empty($attr['order'])? 'id DESC' : $attr['order'];
		$limit = empty($attr['limit'])? 6 : trim($attr['limit']);
		$router= intval(C('URL_ROUTER_ON'));
		$str=<<<str
<?php
\$_table=ucfirst("$table");
\$_tablist=M("\$_table t")
	->join("LEFT JOIN __CATEGORY__ c ON t.cid=c.id")
    ->field("t.*,c.title as cate_title,c.name as cate_name")
    ->where('t.status=1')
    ->order("t.$order")
    ->limit("$limit")
    ->select();
foreach (\$_tablist as \$k => \$v):
	if($router): \$v['url']=U('/'.\$v['cate_name'].'/'.\$v['id'],'','html',true);
	else: \$v['url']=U('Show/index',array('cid'=>\$v['cid'],'id'=>\$v['id']),'html',true);
	endif;
?>
$content
<?php
endforeach;
?>
str;
		return $str;
	}

	public function _position($attr){
		$cid = empty($attr['cid']) ? '' : $attr['cid'];
		if('$' == substr($cid,0,1)){
			$cid_name=substr($cid,1);
			$cid=$this->tpl->tVar[$cid_name];
		}

		$mark = empty($attr['mark'])? '/' : trim($attr['mark']);

        $categorys=D('Common/Category')->getCache();
        $position=\Lib\ArrayTree::getParents($categorys,$cid);

        $str='<a href="'.U('/','','html',true).'">主页</a>';
        foreach ($position as &$v) {
            $str.=' <span>'.$mark.'</span> ';
            $str.='<a href="'.$v['url'].'">'.$v['title'].'</a>';
        }
        return $str;
	}

	public function _nav($attr, $content){
		if(empty($attr['groups'])) return '';
		else $groups = intval($attr['groups']);
		$limit = empty($attr['limit'])? '0,10' : trim($attr['limit']);	
		$str=<<<str
<?php
\$_nav=M('Nav')
	->where("groups={$groups}")
	->limit($limit)
	->getField('id,title,url,remark,image');
if(!empty(\$_nav)):
foreach (\$_nav as \$k => \$v):
?>
$content
<?php
endforeach;
endif;
?>
str;
	    return $str;
	}

	public function _data($attr){
		if(empty($attr['name'])) return '';
		else $name = $attr['name'];
		$value=M('Data')->where("name='{$name}'")->getField('value');
		return $value;
	}




}


?>