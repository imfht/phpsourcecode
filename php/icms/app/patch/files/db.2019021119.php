<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    $date = date("Ymd");
    iDB::query("RENAME TABLE `#iCMS@__spider_project`  TO `#iCMS@__spider_project_".$date."`");
    if(!iDB::check_table('spider_project')){
        iDB::query("
CREATE TABLE `#iCMS@__spider_project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `urls` text NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `rid` int(10) unsigned NOT NULL,
  `poid` int(10) unsigned NOT NULL,
  `auto` tinyint(1) unsigned NOT NULL,
  `lastupdate` int(10) unsigned NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ");
        $msg.='采集方案表升级成功<iCMS>';
    }
    iDB::check_table('spider_project') && $fields  = apps_db::fields('#iCMS@__spider_project');
    if(iDB::check_table('spider_project_'.$date) && $fields['config']){
        $all = iDB::all("SELECT * FROM `#iCMS@__spider_project_".$date."`");
        foreach ($all as $key => $value) {
            $config = array(
                'list_url' =>$value['list_url'],
                'sleep'    =>$value['sleep'],
                'checker'  =>$value['checker'],
                'self'     =>$value['self'],
                'psleep'   =>$value['psleep']
            );
            unset($value['list_url'],$value['sleep'],$value['checker'],$value['self'],$value['psleep']);
            $value['config'] = addslashes(json_encode($config));
            $pid = iDB::insert('spider_project',$value);
            $msg.='采集方案 pid:'.$pid.'转换成功<iCMS>';
        }
    }

    return $msg;
});

