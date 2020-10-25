<?php
namespace Home\TagLib;
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
			'attr'	=> 'cid,limit,order',
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
		//获取当前位置
		'position'	=> array(
			'attr'	=> 'cid',
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
		$limit = empty($attr['limit'])? '9' : intval($attr['limit']) -1;

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

$str = <<<str
<?php
if(!isset(\$categorys)){
	\$categorys=D('Common/Category')->getCache();
}
\$cate=\$categorys[$cid];

if(\$cate['is_menu']){
	foreach (\$categorys as \$k => \$v) {
		if(\$v['pid'] == $cid) \$cid_arr[]=\$v['id'];
	}
    \$map=array('cid'=>array('IN',\$cid_arr));
}else{
    \$map=array('cid'=>$cid);
}

\$table=ucfirst(\$cate['table']);
\$p=I('p',0,'intval');
if($page){
	\$count=M(\$table)->where(\$map)->count();
    \$Page = new \Lib\Page(\$count,$limit);
    \$Page->url = \$cate['name'];
    \$page = \$Page->show();	
}

\$_artlist=M("\$table t")
	->join("LEFT JOIN __CATEGORY__ c ON t.cid=c.id")
    ->field("t.*,c.title as cate_title,c.name as cate_name")
    ->where(\$map)
    ->order("t.$order")
    ->limit("\$p,$limit")
    ->select();

foreach (\$_artlist as \$k => \$v){
	\$v['url']=U('/'.\$v['cate_name'].'/'.\$v['id'],'','html',true);
?>
$content
<?php
}

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

		$mark = empty($attr['mark'])? '>' : trim($attr['mark']);

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
foreach (\$_nav as \$k => \$v) {
?>
$content
<?php
}
?>
str;
	    return $str;
	}

	public function _data($attr){
		if(empty($attr['name'])) return '';
		else $name = $attr['name'];

		$value_data=F('ValueData');
		if(!$value_data){
			$value_data=M('Data')->getField('name,value',true);
			F('ValueData',$value_data);
		}

		if(isset($value_data[$name])) return $value_data[$name];
		else return '';
	}




}


?>