<?php
/**
 * TXTCMS 蜘蛛爬行模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class RobotAction extends AdminAction {
	public function _init(){
		parent::_init();
	}
	public function index(){
		$data=array();
		$pagesize=15;
		$p=isset($_GET['p'])?$_GET['p']:1;	//页数
		$zongshu=0;
		if(is_file(config('ROBOT_FILE'))){
			$arr=file(config('ROBOT_FILE'));
			$arr=str_replace(array("\r\n","\r","\n"),'',$arr);
			$zongshu=count($arr);
			if($zongshu!=0){
				foreach ($arr as $i=>$v) {
					if(trim($arr[$i])=='') continue;
					$id=$zongshu-$i;
					list($ip,$name,$url,$time)=explode("||",$arr[$i]);
					if(date("Y-m-d")==date("Y-m-d",strtotime($time))) $time='<font color=red>'.$time.'</font>';
					$url=htmlspecialchars($url);
					$href=$url;
					if(strlen($url)>65) $href=substr($url,0,65).'...';
					$url='<a target=_blank title="打开此链接" href='.$url.'>'.$href.'</a>';
					$result[]=array('id'=>$id,'name'=>$name,'ip'=>$ip,'url'=>$url,'time'=>$time);
				}
				$total=count($result);
				$totalpages = ceil($total/$pagesize);
				if($p > $totalpages){
					$p = $totalpages;
				}
				$pages = get_page_css($p, $totalpages, 4,url('Admin/Robot/index',array('p'=>'!page!')), false);
			}
			$data['list']=array_slice($result,(($p-1)*$pagesize),$pagesize);
			$data['total']=$total;
			$data['p']=$p;
			$data['pages']=$pages;
		}
		$this->assign($data);
		$this->display();
	}
}