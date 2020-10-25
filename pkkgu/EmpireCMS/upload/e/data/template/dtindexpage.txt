<?php
require('e/class/connect.php');
require('e/class/db_sql.php');
$link=db_connect();
$empire=new mysqlquery();
//缓存
$ecachelastedit=0;
if($public_r['ctimeopen'])
{
	$public_diyr=$empire->fetch1("select fclastindex from {$dbtbpre}enewspublic_fc limit 1");
	$ecachelastedit=$public_diyr['fclastindex'];
}
$ecms_tofunr=array();
$ecms_tofunr['cacheuse']=0;
$ecms_tofunr['cachetype']='indexpage';
$ecms_tofunr['cacheids']='0';
$ecms_tofunr['cachepath']='empirecms';
$ecms_tofunr['cachedatepath']='cindex';
$ecms_tofunr['cachetime']=$public_r['ctimeindex'];
$ecms_tofunr['cachelasttime']=$public_r['ctimelast'];
$ecms_tofunr['cachelastedit']=$ecachelastedit;
$ecms_tofunr['cacheopen']=Ecms_eCacheCheckOpen($ecms_tofunr['cachetime']);
if($ecms_tofunr['cacheopen']==1)
{
	$ecms_tofunr['cacheuse']=Ecms_eCacheOut($ecms_tofunr,0);
}
//缓存
include('e/class/functions.php');
include('e/class/t_functions.php');
include('e/data/dbcache/class.php');
include ECMS_PATH.'e/data/'.LoadLang('pub/fun.php');
//页面
$pr=$empire->fetch1("select sitekey,siteintro from {$dbtbpre}enewspublic limit 1");
$pagetitle=ehtmlspecialchars($public_r['sitename']);
$pagekey=ehtmlspecialchars($pr['sitekey']);
$pagedes=ehtmlspecialchars($pr['siteintro']);
$url="<a href=\"".ReturnSiteIndexUrl()."\">".$fun_r['index']."</a>";//栏目导航
$indextemp=GetIndextemp();//取得模板
$string=DtNewsBq('indexpage',$indextemp,0);
$string=str_replace('[!--newsnav--]',$url,$string);//位置导航
$string=ReplaceSvars($string,$url,0,$pagetitle,$pagekey,$pagedes,$addr,0);
$string=str_replace('[!--page.stats--]','',$string);
//缓存
if($ecms_tofunr['cacheopen']==1)
{
	Ecms_eCacheIn($ecms_tofunr,stripSlashes($string));
}
else
{
	echo stripSlashes($string);
}
//缓存
db_close();
$empire=null;
?>