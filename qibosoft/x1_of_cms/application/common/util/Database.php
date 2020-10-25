<?php

namespace app\common\util;
use think\Db;

//数据库操作模型
class Database {
    protected $pre;
    
    public function __construct(){
        $this->pre = config('database.prefix');
    }
    
    public function list_table(){
        $j = $totalsize = 0;
        $query=Db::query("SHOW TABLE STATUS");
        foreach($query AS $array){
            /*
             if($choose!='all'){
             if($choose=='out'){
             if(ereg("^($pre)",$array[Name])){
             continue;
             }
             }else{
             if(!ereg("^($pre)",$array[Name])){
             continue;
             }
             }
             }*/
            
            if(!preg_match("/^{$this->pre}/",$array['Name'])){
                continue;
            }
            
            $j++;
            $totalsize=$totalsize+$array['Data_length'];
            $array['Data_length']=number_format($array['Data_length']/1024,3);
            $array['j']=$j;
			$array['Annotation']=$array['Comment'];
            $listdb[$array['Name']]=$array;
        }
        return [$totalsize,$listdb];
    }
    
    
    function show_field($table){
        /*
         global $db;
         $query=$db->query(" SELECT * FROM $table limit 0,1");
         $num=mysql_num_fields($query);
         for($i=0;$i<$num;$i++){
         $f_db=mysql_fetch_field($query,$i);
         $field=$f_db->name;
         $show.="`$field`,";
         }
         $show.=")";
         $show=str_replace(",)","",$show);*/
        
        $array = table_field($table,'',false);
        $show = '`'.implode('`,`',$array).'`';
        return $show;
    }
    
    
    function create_table($table,$isup=0){
        //global  $repair;//,$mysqlversion,$Charset;
        $show = $isup ? '' : "DROP TABLE IF EXISTS `$table`;\n";
//         if($repair){
//             Db::execute("OPTIMIZE TABLE `$table`");
//         }
        $array = Db::query("SHOW CREATE TABLE $table");
        
        //if(!$mysqlversion){
        $show .= $array[0]['Create Table'].";\n\n";
        if ($isup) {
            if (!preg_match("/IF NOT EXISTS/i", $show)) {
                $show = str_replace("CREATE TABLE ", "CREATE TABLE IF NOT EXISTS ", $show);
            }            
            $show .="TRUNCATE TABLE  `$table`;\n";
        }
        return $show;
        // }
        /*
         $array['Create Table']=preg_replace("/DEFAULT CHARSET=([0-9a-z]+)/is","",$array['Create Table']);
         
         if($mysqlversion=='new'){
         $Charset || $Charset='latin1';
         $array['Create Table'].=" DEFAULT CHARSET=$Charset";
         }
         $show.=$array['Create Table'].";\n\n";
         return $show;*/
    }
    
    
    function bak_table($table,$start=0,$row=3000,$isup=0){
        global $db;
        $haystack = [
            config('database.prefix').'redis_index',
            config('database.prefix').'redis_list',
            config('database.prefix').'timed_log',
        ];
        if(in_array($table, $haystack)){
            return ;
        }
        $limit=" limit $start,$row ";
        //$field=show_field($table);
        $query = Db::query(" SELECT * FROM `$table` $limit ");

        $fields = $isup ? '(`'.implode('`,`', table_field($table,'',false)).'`)' : '';
        
        //$num=mysql_num_fields($query);
        //$num = count( table_field($table,'',false) );
        //$field_array = table_field($table,'',false);
        //while ($array=mysql_fetch_row($query)){
        $link = function_exists('mysql_escape_string')?:mysqli_connect(config('database.hostname'), config('database.username'), config('database.password'), config('database.database'), config('database.hostport'));
        foreach($query AS $array){
            $rows='';
            
            //for($i=0;$i<$num;$i++){if(function_exists('mysql_escape_string'))print_r($array);exit;
            //    $code = function_exists('mysql_escape_string') ? mysql_escape_string($array[$i]) : addslashes($array[$i]);
            //    $rows.=(is_null($array[$i])?'NULL':"'".$code."'").",";
            // }
            foreach($array AS $field=>$value){
                $rows .= ( is_null($value) ? 'NULL' : "'".( function_exists('mysql_escape_string') ? mysql_escape_string($value) : mysqli_real_escape_string($link,$value) )."'" ).",";
                
            }
            
            $rows=substr($rows,0,-1);
            //$rows.=")";
            //$rows=str_replace(",)","",$rows);
            //$show.="INSERT INTO `$table` ($field) VALUES ($rows);\n";
            $show.="INSERT INTO `$table` $fields VALUES ($rows);\n";
        }
        return $show;
    }
    
    
    function create_table_all($tabledb,$isup){
        foreach($tabledb as $table){
            $show.=$this->create_table($table,$isup)."\n";
        }
        return $show;
    }
    
    //备份数据
    function bak_out($tabledb,$rowsnum,$tableid,$page,$step,$rand_dir,$baksize,$isup){
        //global $rowsnum,$tableid,$page,$step,$rand_dir,$baksize;
        
        //还没有随机生成目录之前
        if(!$rand_dir){
            /*特地处理有些服务器不能创建目录的情况,此时必须手工创建mysql目录*/
            if( file_exists(RUNTIME_PATH."mysql_bak/mysql_initial") )
            {
                if( !is_writable(RUNTIME_PATH."mysql_bak/mysql") ){
                    showmsg(RUNTIME_PATH."mysql_bak/mysql目录不可写,请改属性为0777");
                }
                $rand_dir="mysql";
                
                $d=opendir(RUNTIME_PATH."mysql_bak/mysql/");
                while($f=readdir($d)){
                    if(preg_match("/\.sql$/i",$f)){
                        unlink(RUNTIME_PATH."mysql_bak/mysql/$f");
                    }
                }
                
                // write_file(RUNTIME_PATH."mysql_bak/mysql/index.php",str_replace('<?php die();','<?php',read_file('mysql_into.php')));
                $show = $this->create_table_all($tabledb,$isup);	//备份数据表结构
                //$db->query("TRUNCATE TABLE {$pre}bak");
                //bak_dir('../data{$webdb[web_dir]}/');		//备份缓存
            }else{
                $rand_dir = date("Y-m-d_His_.",time()).strtolower(rands(3));
                $show = $this->create_table_all($tabledb,$isup);	//备份数据表结构
                if( !file_exists(RUNTIME_PATH."mysql_bak") ){
                    if( !@mkdir(RUNTIME_PATH."mysql_bak",0777) ){
                        showmsg(RUNTIME_PATH."mysql_bak目录不能创建");
                    }
                }
                if(	!@mkdir(RUNTIME_PATH."mysql_bak/$rand_dir",0777)	)
                {
                    showmsg(RUNTIME_PATH."mysql_bak/$rand_dir,目录不可写,请改属性为0777");
                }
                //复制一个自动还原的文件到SQL目录.方便日后还原
                // write_file(RUNTIME_PATH."mysql_bak/$rand_dir/index.php",str_replace('<?php die();','<?php',read_file('mysql_into.php')));
                //$db->query("TRUNCATE TABLE {$pre}bak");
                //bak_dir('../data{$webdb[web_dir]}/');		//备份缓存
            }
        }
        !$rowsnum && $rowsnum=500;	//每次读取多少条数据
        //此page指的是每个表大的时候.需要多次跳转页面读取
        if(!$page)
        {
            $page=1;
        }
        $min=($page-1)*$rowsnum;
        $tableid=intval($tableid);
        
        //$show.=$tablerows=bak_table($tabledb[$tableid],$min,$rowsnum,$isup);
        //当前表能取到数据时,继续此表下一页取数据,否则从下一个表的0开始
        
        if( $tablerows = $this->bak_table($tabledb[$tableid],$min,$rowsnum,$isup) )
        {
            $show.=$tablerows;
            unset($tablerows);	//释放内存
            $page++;
        }
        else
        {
            $page=0;
            $tableid++;
        }
        
        //分卷是从0开始的
        $step=intval($step);
        $filename="$step.sql";
        write_file(RUNTIME_PATH."mysql_bak/".$rand_dir."/".$filename,$show,'a+');
        
        //如果不指定每卷大小.将默认为1M
        $baksize=$baksize?$baksize:1024;
        
        //对文件做精确大小分卷处理
        $step = $this->cksize(RUNTIME_PATH."mysql_bak/".$rand_dir."/".$filename,$step,1024*$baksize);
        
        //如果还存在表时.继续,否则结束
        if($tabledb[$tableid])
        {
            foreach($tabledb as $value)
            {
                $Table.="$value|";
            }
            //记录下来.防止中途备份失败
            write_file(RUNTIME_PATH."bak_mysql.txt",mymd5("index.php?lfj=$lfj&action=out&page=$page&rowsnum=$rowsnum&tableid=$tableid&rand_dir=$rand_dir&step=$step&tabledbreto=$Table&baksize=$baksize&isup=$isup"));
            
            echo "<CENTER>已备份 <font color=red>$step</font> 卷, 进度条 <font color=blue>{$page}</font> 当前正在备份数据库 <font color=red>$tabledb[$tableid]</font></CENTER>";
            
            $url = url('backup',"page=$page&rowsnum=$rowsnum&tableid=$tableid&rand_dir=$rand_dir&step=$step&baksize=$baksize&isup=$isup");
            
            //$rowsnum,$tableid,$page,$step,$rand_dir,$baksize
            
            print<<<EOT
<form name="form1" method="post" action="$url">
  <input type="hidden" name="tabledbreto" value="$Table">
</form>
<SCRIPT LANGUAGE="JavaScript">
<!--
function autosub(){
	document.form1.submit();
}
autosub();
//-->
</SCRIPT>
EOT;
            //echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=index.php?lfj=$lfj&action=out&page=$page&rowsnum=$rowsnum&tableid=$tableid&rand_dir=$rand_dir&step=$step&tabledbreto=$Table&baksize=$baksize'>";
            exit;
        }
        else
        {
            $dir=opendir(RUNTIME_PATH."mysql_bak/$rand_dir");
            while($file=readdir($dir)){
                if(preg_match('/.sql$/',$file))
                {
                    $totalsize+=$sqlfilesize=@filesize(RUNTIME_PATH."mysql_bak/$rand_dir/$file");
                    $rs[sqlsize][]=number_format($sqlfilesize/1024,3);
                }
                
            }
            $totalsize=number_format($totalsize/1048576,3);
            @unlink(RUNTIME_PATH."bak_mysql.txt");
            $rs['totalsize']=$totalsize;
            $rs['timedir']=$rand_dir;
            if( !@is_writable(RUNTIME_PATH."mysql_bak/$rand_dir/0.sql") ){
                showmsg("备份失败，请在/mysql_bak/目录下创建一个目录mysql然后改其属性为0777,如果此目录已存在，请删除他，重新创建，并改属性为0777");
            }
            return $rs;
        }
    }
    
    function bak_time(){
        global $webdb;
        $show="<select  name='baktime'><option value='' selected>请选择备份文件</option>";
        $dir=opendir(RUNTIME_PATH.'mysql_bak/');
        while( $file=readdir($dir) ){
            if( is_dir(RUNTIME_PATH."mysql_bak/$file") && $file!='.' && $file!='..' ){
                $show.="<option value='$file'>$file</option>";
            }
        }
        $show.="</select>";
        return $show;
    }
    
    function bak_into($baktime,$step){
        $step=intval($step);
        $file=RUNTIME_PATH."mysql_bak/$baktime/{$step}.sql";
        if( file_exists($file) ){
            $sql = read_file($file);
            $array = explode(";\n", $sql);
            foreach ($array AS $value){
                if(trim($value)){
                    Db::execute($value);
                }
            }
            
        }
        $step++;
        if( file_exists(RUNTIME_PATH."mysql_bak/$baktime/{$step}.sql") ){
            
            $url = url('into',['baktime'=>$baktime,'step'=>$step,'goto'=>1]);
            write_file(RUNTIME_PATH."mysql_insert.txt",$url);
            echo "已导入第 {$step} 卷<META HTTP-EQUIV=REFRESH CONTENT='0;URL=$url'>";
            exit;
        }else{
            //$query=$db->query("SELECT * FROM {$pre}bak ");
            //while(@extract($db->fetch_array($query))){
            //	write_file(ROOT_PATH."A/$bak_dir",$bak_txt);
            //}
            @unlink(RUNTIME_PATH."mysql_insert.txt");
            $url = url('into');
            jump("导入完毕",$url,'5');
        }
    }
    function cksize($lastSqlFile,$step,$size){
        if( @filesize($lastSqlFile)<($size+10*1024) )
        {
            return $step;
        }
        //复制一份最后生成的大于指定大小的SQL文件做处理
        copy($lastSqlFile,"{$lastSqlFile}.bak");
        $filePre=str_replace(basename($lastSqlFile),"",$lastSqlFile);
        $readfile=read_file("{$lastSqlFile}.bak");
        $detail=explode("\n",$readfile);
        unset($readfile); //释放内存
        foreach($detail AS $key=>$value){
            $NewSql.="$value\n";
            if(strlen($NewSql)>$size && (strstr($value,'INSERT INTO ')||strstr($value,' CHARSET='))){
                write_file("$filePre/$step.sql",$NewSql);
                $step++;
                $NewSql='';
            }
        }
        //余下的再写进新文件,此时step已经累加过了
        if($NewSql){
            write_file("$filePre/$step.sql",$NewSql);
        }
        @unlink("{$lastSqlFile}.bak");
        return $step;
    }
}