<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/6/20 0020
 * Time: 14:49
 */
 //Mysql常用的操作
class MysqlOp {

    //转移用户数据 赋值一个数据表的某些字段到某个数据库另一个表的某些字段
    public  static  function  import_data($config=array()){
        //如果直接是克隆表就使用方式2
        $default_config=array(  //如果没有指定数据库使用默认config.ini.php配置的数据库，就是表的复制了
            'table_source'=>'mall2/jn_member',
            'table_target'=>'center_test2/m_user2',  //不存在表或者数据库自动创建
            'columns'=>array(  //列对应，要复制的部分
                'member_id'=>'user_id',
                'member_name'=>'user_name',
                'member_mobile'=>'user_mobile'
            ),
            'limit'=>1000, //限制数据条数 ，为0则不限制
        );
        $config=array_merge($default_config,$config);//与默认配置合并

        $source=explode('/',$config['table_source']);
        $target=explode('/',$config['table_target']) ;

        //先尝试创建数据库和表然后再插入数据，如果表已经有数据则清空原数据，重新覆盖
        $link= MYSQL::getLink(null,2);//假设在同一个服务器的数据库，如果不是的话，请给getLink传入不同服务器的参数
        $flag1= mysqli_select_db( $link,$source[0]) or die ('table '.$source[0].' not exist!' . mysqli_error($link));
        //选取所有数据库查看//原数据表是否存在
        //数据表列是否存在  //切换库到源库
        $sql=sprintf("show TABLES from %s",$source[0]);
        $result= MYSQL::query($sql);
        $key1=sprintf('Tables_in_%s',$source[0]);
        $table_arr=array();
        foreach($result as $key=>$value){
            array_push($table_arr,$value[$key1]);
        }

        if(!in_array($source[1],$table_arr)){throw new Exception(sprintf("数据表%s并不在数据库%s中",$source[1],$source[0]));} ;
        //查询表数据结构  ,切换数据库
        MYSQL::execute(sprintf('use %s;',$source[0]));
        $result= MYSQL::query(sprintf('SHOW CREATE TABLE %s;',$source[1]));

        $content=$result[0]['Create Table'];

        $lines=explode(',',$content);
        // var_dump($lines);
        //创建克隆表的语句
        $pre_str=sprintf('DROP TABLE IF EXISTS `%s`;'.chr(10),$target[1]);

        $columns=$config['columns'];
        $new_create=array();

        $first_line=explode(chr(10),$lines[0]);

        //创建table
        array_push($new_create,str_replace(sprintf('`%s`',$source[1]),sprintf('`%s`',$target[1]),$first_line[0]));
        $lines[0]=$first_line[1];
        foreach($lines as $key=>$value){

            $pattern='~`(.+?)`~';
            preg_match($pattern,$value,$matches);
            if($matches&&array_key_exists($matches[1],$columns)){
                $column=$matches[1];
                $new_value=str_replace(sprintf('`%s`',$column),sprintf('`%s`',$columns[$column]?$columns[$column]:$column),$value);
                array_push($new_create,$new_value);
            }
        }
        $first_line=$new_create[0];unset($new_create[0]);
        $create_new=$pre_str.$first_line.implode(',',$new_create);
        //在新的数据库执行创建语句
        //如果目标数据库不存在则创建

        $flag1= mysqli_select_db($link,$target[0]);
        if(!$flag1){ //不存在目标数据库，直接创建
            $sql=sprintf('CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARSET utf8 COLLATE utf8_general_ci',$target[0]);
            MYSQL::execute($sql);
        }
        //查询表数据结构  ,切换数据库
        MYSQL::execute(sprintf('use %s;',$target[0]));
        $result= MYSQL::multiSql($create_new);
        if($result){  //查询和插入数据,使用事务插入
            $filed='';$filed_target='';
            foreach($columns as $key=>$value){
                $filed.=','.$key;
                $value=$value?$value:$key;
                $filed_target.=','.$value;
            }
            $filed=substr($filed,1); $filed_target=substr($filed_target,1);
            if($config['limit']==0){
                $sql=sprintf('select %s from %s ',$filed,$source[1]);
            }else{
                $sql=sprintf('select %s from %s limit  %s',$filed,$source[1],$config['limit']);
            }
            //执行
            MYSQL::execute(sprintf('use %s;',$source[0]));
            $data_set1=  MYSQL::query($sql);
            $insert=array();
            foreach($data_set1 as $key=>$value){
                $values='';
                foreach($value as $value2){
                    $values.=','."'".$value2."'";
                }
                $values=substr($values,1);
                $s=sprintf('insert into %s (%s)VALUES(%s) ',$target[1],$filed_target,$values);
                array_push($insert,$s);
            }
            $insert=implode(';',$insert);
            MYSQL::execute(sprintf('use %s;',$target[0]));
            MYSQL::beginTransaction();

            MYSQL::multiSql($insert);
            MYSQL::commit();

            echo 'ok';

        }
        //向创建语句内插入数据
    }

    //克隆数据库,如果目标数据库不存在则自动创建
    public  static  function  clone_db($config=array()){
        $default_config=array(
            'table_source'=>'table_default',
            'table_target'=>'table_new',  //不存在表或者数据库自动创建
        );
        $config=array_merge($default_config,$config);//与默认配置合并
        $source=$config['table_source'];
        $target=$config['table_target'];

        //判断目标数据库是否存在，不存在则创建
        $link= MYSQL::getLink(null,2);//假设在同一个服务器的数据库，如果不是的话，请给getLink传入不同服务器的参数
        $flag1= mysqli_select_db( $link,$target);
        if(!$flag1){ //不存在目标数据库，直接创建
            $sql=sprintf('CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARSET utf8 COLLATE utf8_general_ci',$target);
            MYSQL::execute($sql);
        }
        $app_config=c('db');
        $db_config=$app_config['master'];

        //mysqldump  -h172.17.0.3 -uroot -p111111 --add-drop-table  fcrm_default| mysql -h172.17.0.3 -uroot -p111111 fcrm43
        $cmd=sprintf('mysqldump -h%s -u%s -p%s -P%s --add-drop-table %s| mysql -h%s -u%s -p%s -P%s %s',$db_config['dbhost'],$db_config['dbuser'],$db_config['dbpwd'],$db_config['dbport'],$source,$db_config['dbhost'],$db_config['dbuser'],$db_config['dbpwd'],$db_config['dbport'],$target);
        //执行克隆命令
        exec($cmd,$out);
        return $out;
    }


    //克隆表到另一个数据库中某张表中，如果目标数据库或表不存在将自动创建
    public  static  function  clone_table($config=array()){

        $default_config=array(
            'table_source'=>'mall2/jn_member',
            'table_target'=>'center_test2/m_user2',  //不存在表或者数据库自动创建
            'limit'=>1000,
            'clone_data'=>true  //是否连同数据一同克隆
        );
        $config=array_merge($default_config,$config);//与默认配置合并
        //直接导出数据和运行
        $source=explode('/',$config['table_source']);
        $target=explode('/',$config['table_target']) ;

        //判断目标数据库是否存在，不存在则创建
        $link= MYSQL::getLink(null,2);//假设在同一个服务器的数据库，如果不是的话，请给getLink传入不同服务器的参数
        $flag1= mysqli_select_db($link,$target[0]);
        if(!$flag1){ //不存在目标数据库，直接创建
            $sql=sprintf('CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARSET utf8 COLLATE utf8_general_ci',$target[0]);
            MYSQL::execute($sql);
        }
        //查询表数据结构  ,切换数据库
        MYSQL::execute(sprintf('use %s;',$target[0]));
        //创建表
        MYSQL::execute(sprintf('DROP TABLE  IF EXISTS `%s`;',$target[1]));//先删除
        $result= MYSQL::execute(sprintf('CREATE TABLE %s LIKE %s.%s',$target[1],$source[0],$source[1]));
        //赋值数据
        if($config['clone_data']){
       $result=  MYSQL::execute(sprintf('INSERT %s SELECT * FROM %s.%s',$target[1],$source[0],$source[1]));
        }

        return $result;
    }

    //获取表所有字段  三种类型，0 返回原型 1 返回精简数组 2 返回字符串
    public  static  function  get_field($table_name='',$type=''){

        $sql='SHOW COLUMNS FROM  '.$table_name;
        $columns=Model3()->query($sql);
        if($type==1){//是否精简化
            $column=array();
            foreach($columns as $value){
                array_push($column,$value['Field']);
            }
            $columns=$column;
        }
        else if($type==2){//是否精简化
            $column='';
            foreach($columns as $value){
                $column.=','.$value['Field'];
            }
            $columns=substr($column,1);
        }
        return $columns;

    }

    //备份数据表到文件
    public  static  function  backup_table($filename,$config=array()){

        $default_config=array(
            'table'=>'center_test2/m_user2',
            'backdata'=>true //是否包含数据
        );

        $config=array_merge($default_config,$config);//与默认配置合并
        //配置
        $config=array_merge($default_config,$config);//与默认配置合并
        if(!self::check_db($config['table'],false)){
            exit('备份的数据库或数据表不存在');
        }
         $table_arr=explode('/',$config['table']);
        //使用命令备份数据库
        //找到mysql位置
        $sql="show variables like 'basedir' ";
        $data=MYSQL::query($sql);
        $exe_path=$data[0]['Value'].'bin/mysqldump.exe';
        $app_config=c('db');
        $db_config=$app_config['master'];
        //执行命令mysqldump  -uroot -proot --default-character-set=utf8  mall  jn_goods_class> a.sql
        $cmd=sprintf('%s -u%s -p%s --default-character-set=utf8  %s %s > %s ',$exe_path,$db_config['dbuser'],$db_config['dbpwd'],$table_arr[0],$table_arr[1],$filename);
         if(!$config['backdata']){ //仅包含结构
             $cmd=sprintf('%s -u%s -p%s --default-character-set=utf8  %s %s -d> %s ',$exe_path,$db_config['dbuser'],$db_config['dbpwd'],$table_arr[0],$table_arr[1],$filename);
         }

        exec($cmd,$out);
       return true;
    }
    //恢复文件到数据表
    public  static  function  recover_table($filename,$config=array()){
        $default_config=array(
            'table'=>'center_test2/m_user2'
        );
        $config=array_merge($default_config,$config);//与默认配置合并
        if(!file_exists($filename)){
            exit('recover file not exist');
        }
         //
        $table_arr=explode('/',$config['table']);
        self::check_db_exist($table_arr[0],true); //没有则创建数据库和表
        $sql="show variables like 'basedir' ";
        $data=MYSQL::query($sql);
        $exe_path=$data[0]['Value'].'bin/mysql.exe';
        $app_config=c('db');
        $db_config=$app_config['master'];
        //执行命令
        $cmd=sprintf('%s -u%s -p%s  %s  < %s ',$exe_path,$db_config['dbuser'],$db_config['dbpwd'], $table_arr[0],$filename);
       //file_put_contents('s.html',$cmd);
        exec($cmd);
        return true;


    }

    //备份数据库到文件,添加mysql_path到环境变量
    public  static  function  backup_database($filename,$config=array()){

        $default_config=array(
            'database'=>'center_test2',
            'backdata'=>false,  //连同数据一起备份，否则只备份结构
        );
        $config=array_merge($default_config,$config);//与默认配置合并
         if(!self::check_db_exist($config['database'],false)){
             return array('flag'=>false,'msg'=>'备份的数据库不存在');
         }
        //使用命令备份数据库
       //找到mysql位置
       $sql="show variables like 'basedir' ";
        $data=MYSQL::query($sql);
        $exe_path=$data[0]['Value'].'bin/mysqldump.exe';
        $app_config=c('db');
        $db_config=$app_config['master'];

       //执行命令
       $cmd=sprintf('%s -u%s -p%s   --extended-insert=false --default-character-set=utf8 %s > %s ','mysqldump.exe',$db_config['dbuser'],$db_config['dbpwd'],$config['database'],$filename);
      if(!$config['backdata']){ //如果仅仅是备份结构
          $cmd=sprintf('%s -u%s -p%s --default-character-set=utf8   %s  -d> %s ',$exe_path,$db_config['dbuser'],$db_config['dbpwd'],$config['database'],$filename);
      }
         exec($cmd,$out);
      return true;
      //  $path=BASE_PATH.'/data';
      //  pclose(popen("$cmd >> $path/log.txt&", 'r')); //在timer.php的输出会到log.txt
    }

    //恢复
    public  static  function  recover_database($filename,$config=array()){
          //默认配置
        $default_config=array(
            'database'=>'center_test2',
            'backdata'=>true,  //连同数据一起恢复，否则只恢复数据结构
        );
        $config=array_merge($default_config,$config);//与默认配置合并
        if(!file_exists($filename)){
            exit('recover file not exist');
        }
        self::check_db_exist($config['database'],true);
        $sql="show variables like 'basedir' ";
        $data=MYSQL::query($sql);
        $exe_path=$data[0]['Value'].'bin/mysql.exe';
        $app_config=c('db');
        $db_config=$app_config['master'];
        //执行命令
        //mysql -uroot -proot meirong  <meirong2.sql
        $cmd=sprintf('%s -u%s -p%s  %s  < %s ',$exe_path,$db_config['dbuser'],$db_config['dbpwd'],$config['database'],$filename);
        exec($cmd);
        return true;
    }

    //检查数据库和数据表的存在性，并能设置自动创建，默认不自动创建，格式 mall2/jn_member
    public  static  function  check_db($db,$auto_create=false){
        if(strstr($db,'/')){  //检查数据库和表
            $target=explode('/',$db);
            if(self::check_db_exist($target[0],$auto_create)){ //数据库存在
                //检查表
                return self::check_table_exist($target[0],$target[1],$auto_create);
            }
            else if($auto_create){ //数据库不存在且自动创建的情况
                return self::check_table_exist($target[0],$target[1],$auto_create);
            }
            else{
                return false;//返回原始结果
            }

        }
        else{  //只检测数据库
       return self::check_db_exist($db,$auto_create);
        }
    }
   //检查指定数据库,或者数据表是否存在,第二个参数为是否自动创建的意思，如果不存在自动创建
    public  static  function  check_db_exist($db,$auto_create=false){
        $link= MYSQL::getLink(null,2);
        $flag1= mysqli_select_db($link,$db);
        if($flag1){
            return true;
        }
        else if($auto_create){
            $sql=sprintf('CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARSET utf8 COLLATE utf8_general_ci',$db);
            MYSQL::execute($sql);
        }
        else{
            return false;
        }
    }
   //查看数据库某个表是否存在,数据库必须是已经存在的
    public  static  function  check_table_exist($database,$table,$auto_create=false){

        $sql=sprintf("show TABLES from %s",$database);
        $result= MYSQL::query($sql);
        $result=$result?$result:array();
        $key1=sprintf('Tables_in_%s',$database);
        $table_arr=array();
        foreach($result as $key=>$value){
            array_push($table_arr,$value[$key1]);
        }
        if(in_array($table,$table_arr)){
            return true;
        }
        else if($auto_create){
        $sql=sprintf('use %s; CREATE TABLE `%s` ( `id` int(11) NOT NULL AUTO_INCREMENT,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4',$database,$table);
         MYSQL::multiSql($sql);
            return false;
        }
        else{
            return false;
        }
    }
   //从文件中恢复整个数据表或者数据库






}