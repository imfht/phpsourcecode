<?php

/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2015/8/19 0019
 * Time: 11:30
 * lcc model再次升级版本，可以兼顾mysqli底层驱动，可以程序执行前配置连接信息 增加pre
 */

/*使用前配置
        Model3::set_link('db_name','db_prefix');//设置数据库
 * */

//底层执行类
class MYSQL{
    //private static  $default_link=array('db_host'=>'','db_user'=>'','db_pwd'=>'','db_name'=>'','db_port'=>,'db_charset'=>'');
    private static $link ='';//连接对象
    private static $iftransacte = true;
    public  static $Link_arr=array();

    /*$config格式和之前config一致 连接和设置查询编码方式 使用新的配置数组等**/
    public  static  function   connect($config=array()){
        if(!$config){//如果config文件不存在就取配置文件的连接
            if(self::$Link_arr){$config=self::$Link_arr;}
            else{
                die('from model3.php line27,no db config set in $config[db_config]  or no param take in to the function connect');
            }
        }
        self::$link = @new mysqli($config['db_host'], $config['db_user'], $config['db_pwd'], $config['db_name'], $config['db_port']);
        if (mysqli_connect_errno()) {print_stack(); die("Db Error: database connect failed".mysqli_connect_errno());}
        //$time1=microtime(true);
        //数据库默认就是utf8编码的,不需要专门设置

        if($config['db_charset']=='gbk'){//如果是gbk的编码方式
            $query_string = "
		   			    SET CHARACTER_SET_CLIENT = gbk,
		                 CHARACTER_SET_CONNECTION = gbk,
		                 CHARACTER_SET_DATABASE = gbk,
		                 CHARACTER_SET_RESULTS = gbk,
		                 CHARACTER_SET_SERVER = gbk,
		                 COLLATION_CONNECTION = gbk_chinese_ci,
		                 COLLATION_DATABASE = gbk_chinese_ci,
		                 COLLATION_SERVER = gbk_chinese_ci,
		                 sql_mode=''";
        }
        else{//其它都默认设置utf-8的编码方式
            $query_string = "
		                 SET CHARACTER_SET_CLIENT = utf8,
		                 CHARACTER_SET_CONNECTION = utf8,
		                 CHARACTER_SET_DATABASE = utf8,
		                 CHARACTER_SET_RESULTS = utf8,
		                 CHARACTER_SET_SERVER = utf8,
		                 COLLATION_CONNECTION = utf8_general_ci,
		                 COLLATION_DATABASE = utf8_general_ci,
		                 COLLATION_SERVER = utf8_general_ci,
		                 sql_mode=''";
        }
        //进行编码声明
        if (!self::$link->query($query_string)){
            $error_str="Db Error: ".mysqli_error(self::$link);
            $last_sql='<br/><br/>lastSql: '.Model3::get_last_sql();
            //return $error_str;
            die($error_str.$last_sql.'<br/> '.print_stack());
        }
    }

    /*2015-9-21 lcc 执行删除，修改，添加等操作 第二个参数是需要设定连接数据库时选定**/
    public  static  function  execute ($sql,$config=null){

        if($config)self::connect($config);
        if(!is_object(self::$link)){self::connect();}

        $query = self::$link->query($sql);

        if ($query === false){

            $error = 'Db Error: '.mysqli_error(self::$link);
            throw new Exception($error.'<br/>'.$sql); //抛出异常而不是停止运行
            //exit($error.'<br/>'.$sql);
        }else {
            return $query;
        }

    }
    /*2015-9-21 lcc 执行查询的操作**/
    public  static  function  query($sql,$config=null){

        if($config)self::connect($config);
        if(!is_object(self::$link)){self::connect();}//还没有连接过使用默认链接函数连接

        $result = self::$link->query($sql);
        if ($result === false){

            $error_str="Db Error: ".mysqli_error(self::$link);
            throw new Exception($error_str);
            $last_sql='<br/><br/>lastSql: '.Model3::get_last_sql();
            echo $error_str.$last_sql.'<br/> '.print_stack();
            return false;
        }
        while ($tmp=mysqli_fetch_array($result,MYSQLI_ASSOC)){
            $array[] = $tmp;
        }
        return !empty($array) ? $array : array();
    }
    //获取连接对象 type=1获取一个mysql对象，type=2获取一个mysql的连接
    public  static  function  getLink($config=array(),$type=1){
        if($type==1){
            if($config)self::connect($config);
            if(!is_object(self::$link)){self::connect();}
            return self::$link;
        }
        else if($type==2){
            if(!$config){$config=self::$Link_arr;}
            $lnk = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pwd'],$config['db_name'],$config['db_port']);
            if (mysqli_connect_errno($lnk))
            {
                echo "连接 MySQL 失败: " . mysqli_connect_error();exit;
            }

          return $lnk;
        }
        else if($type==3){return self::$link;}
        else{
            return '';
        }
    }

    //ping并尝试重新连接
    public  static  function ping(){
        $result=false;
        if(self::$link){
            $result=mysqli_ping(self::$link);
        }
        if(!$result){
            if(self::$link){
                mysqli_close(self::$link); //注意：一定要先执行数据库关闭，这是关键
            }
            self::connect();
            $result='reconnect';
        }
       return $result;
    }

    public  static  function  close(){
        if(self::$link){
          return   mysqli_close(self::$link); //注意：一定要先执行数据库关闭，这是关键
        }
        return false;
    }

    public  static  function info(){
        self::ping();//保证连接
        return array(
            'info'=>self::$link->info,
            'server_info'=>self::$link->server_info,
            'server_version'=>self::$link->server_version,
            'client_info'=>self::$link->client_info,
            'client_version'=>self::$link->client_version,
            'host_info'=>self::$link->host_info,
            'warning_count'=>self::$link->warning_count,
            'sqlstate'=>self::$link->sqlstate,
            'protocol_version'=>self::$link->protocol_version,
            'insert_id'=>self::$link->insert_id,
            'thread_id'=>self::$link->thread_id,
            'error_list'=>self::$link->error_list
        );
    }
    public  static  function  insert_id(){
        return self::$link->insert_id;
    }

    /**
     * 取得上一步插入产生的ID
     *
     * @return int
     */
    public static function getLastId($config = null){
        if($config)self::connect($config);
        if(!is_object(self::$link)){self::connect();}//还没有连接过使用默认链接函数连接

        $id = mysqli_insert_id(self::$link);
        if (!$id){
            $result =  self::$link->query('SELECT last_insert_id() as id',$config);
            if ($result === false) return false;
            $id = mysqli_fetch_array($result,MYSQLI_ASSOC);
            $id = $id['id'];
        }
        return $id;
    }
    /**事务* 2015-9-25*/
    public static function beginTransaction($config = null){
        if($config)self::connect($config);
        if(!is_object(self::$link)){self::connect();}//还没有连接过使用默认链接函数连接

        if (self::$iftransacte){
            self::$link->autocommit(false);//关闭自动提交
        }
        self::$iftransacte = false;
    }
    /**提交事务 2015-9-25**/
    public static function commit(){
        if (!self::$iftransacte){
            $result = self::$link->commit();
            self::$link->autocommit(true);//开启自动提交
            self::$iftransacte = true;
            if (!$result) throw_exception("Db Error: ".mysqli_error(self::$link));
        }
    }
    /**回滚 lcc 2015-9-25**/
    public static function rollback(){
        if (!self::$iftransacte){
            $result = self::$link->rollback();
            self::$link->autocommit(true);
            self::$iftransacte = true;
            if (!$result) throw_exception("Db Error: ".mysqli_error(self::$link));
        }
    }


    public  static  function multiSql($sql){
        $sql_arr=explode(';',$sql);
        $result='';
        foreach($sql_arr as $value){
            $value=trim($value); //去除空格造成的影响
            if($value){
                $result.= MYSQL::execute($value);//执行语句
            }
        }
        return $result;
    }
}

class Model3
{
    protected  static $record=false;//是否开始记录，决定alter_sql和cancel_sql是否生效，且清空之前的记录
    protected  static $last_sql;//最后一条sql语句
    protected  static $alter_sql;//所有更新语句，包括插入删除和修改
    protected  static  $cancel_sql;//所有可撤销的语句，对alter_sql语句的逆向
    protected $Table_name = '';
    protected $Where = '';
    protected $Filed = '*';
    protected $Condition = array('limit' => '', 'order' => '', 'group' => '');
    protected $Is_preview = false;//是否是sql预览
    protected $Is_prefixreplace = true;//是否是sql预览


    //设置链接
    public  static  function  set_link($db_name='',$db_prefix='',$ip='127.0.0.1',$port='3306',$user='root',$pass='root',$charset='utf-8'){

        MYSQL::$Link_arr=array(
            'db_host'=>$ip,
            'db_user'=>$user,
            'db_pwd'=>$pass,
            'db_name'=>$db_name,
            'db_port'=>$port,
            'db_charset'=>$charset,
            'db_prefix'=>$db_prefix);
    }


    //构造函数
    public function __construct($name = '')
    {
        $this->Table_name =MYSQL::$Link_arr['db_prefix'].$name;
    }

    //查询操作
    public function  query($sql)
    {
        return MYSQL::query($this->format_sql($sql));
    }

    //执行操作
    public  function   execute($sql)
    {
        return MYSQL::execute($this->format_sql($sql));//不是搜索的查询
    }
    public static  function beginTransaction()//开始事务
    {
        MYSQL::beginTransaction();
    }

    public  static function record(){
        self::$record=true;
        self::$cancel_sql='';
        self::$alter_sql='';
    }
   //开始记录，记录后alter和cancel才开始生效
    public  static function start_record(){
        self::$record=true;
    }
    //获取记录
    public  static  function get_record(){
       return array(
           'cancel'=>self::$cancel_sql,
            'alter'=>self::$alter_sql
       );
    }
    //添加取消语句
    public  static  function  add_cancel($sql){
        self::$cancel_sql.=';'.$sql;
    }
    //清空记录
    public  static  function  clear_record(){
        self::$cancel_sql='';
        self::$alter_sql='';
    }
    //也是开始事务的同名函数，同一个意思
    public static  function begin()//开始事务
    {
        MYSQL::beginTransaction();
    }

    public static  function commit()
    {
        MYSQL::commit();
    }

    //执行多条sql语句，语句以分号间隔,会以事务方式提交,一条失败不会提交
    public  static  function  multiSql($sql){
        $sql_arr=explode(';',$sql);
        // $result=[];
        self::begin();
        foreach($sql_arr as $value){
            $value=trim($value); //去除空格造成的影响
            if($value){
                MYSQL::execute($value);//执行语句
            }
        }
        self::commit();
        return 'ok';
    }
    //回滚
    public  static  function  rollback(){
        MYSQL::rollback();
    }
    //选择
    public   function  select($sql = '')
    {
        if ($sql) {
            //直接使用和执行
        } else {
            $sql = sprintf("select %s  from %s ", $this->Filed, $this->Table_name);
            if ($this->Where) {//如果有where
                $sql .= ' where ' . $this->Where;
            }

            //先group
            if ($this->Condition['group']) {
                $sql .= ' group by ' . $this->Condition['group'];
            }
            if ($this->Condition['order']) {
                $sql .= ' order by ' . $this->Condition['order'];
            }
            if ($this->Condition['limit']) {
                $sql .= ' limit ' . $this->Condition['limit'];
            }

        }
        $sql=$this->format_sql($sql);
        if ($this->Is_preview) {//如果是预览直接返回sql语句
            return $sql;
        } else {
            Model3::$last_sql = $sql;
            return MYSQL::query($sql);//不是搜索的查询
        }
    }

    /**仅查询第一条数据,没有值返回空数组*/
    public function  find()
    {
        $ret=$this->limit(1)->select();
        if ($this->Is_preview) {
            return $ret;
        } else {
            return $ret?$ret[0]:array();
        }
    }

    //数目查询
    public function  count($sql = '')
    {
        //  write($sql);
        $this->Filed = 'count(*) as num';
        $result = $this->select($sql);
        if ($this->Is_preview) {
            return $result;
        } else {
            return intval($result[0]['num']);
        }
    }
    //将字符串里的#替换为表前缀
    private  function  format_sql($sql){
           if($this->Is_prefixreplace){
               $prefix=MYSQL::$Link_arr['db_prefix'];
               return str_replace('#',$prefix,$sql);
           }
        else{
            return $sql;
        }

    }
    //是否对字符串中的#号进行前缀替换
    public function prefix_replace($flag=true)
    {
        $this->Is_prefixreplace =$flag;
        return $this;
    }

    public function  update($update)
    {
        if (!$update) {
            return false;
        } else if (is_string($update)) {
            $str = $update;
            $sql = sprintf("update %s set %s where %s", $this->Table_name, $str, $this->Where);
        } else if (is_array($update)) {
            $str = '';
            $link=MYSQL::getLink(false,1);//获取连接
            foreach ($update as $key => $value) {

                if(strstr($key,'PRE')){//如果键值包含PRE，直接连接value
                    $str.=','.$value;
                    /*$str_arr=explode(' ',$key);

                    if(count($str_arr)>1){
                        $str.=','.$str_arr[0].'='.$value;
                    }
                    else{
                        $str .= ",$key='$value'";
                    }*/
                    //  $str.=$value;continue;//下一次循环
                }
                else{
                    //替换单引号
                  //  $value= str_replace("'",'’',$value);//替换单引号以免出问题
                    $value =mysqli_real_escape_string($link,$value);
                    $str .= ",$key='$value'";
                }

            }
            $str = substr($str, 1);
            $sql = sprintf("update %s set %s where %s", $this->Table_name, $str, $this->Where);
        } else {
            return false;
        }
        if ($this->Is_preview) {//如果是预览直接返回sql语句
            return $sql;
        } else {
            Model3::$last_sql = $sql;
            if(self::$record){//如果执行记录
              self::$alter_sql.=';'.$sql;
            }
            return MYSQL::execute($this->format_sql($sql));//不是搜索的查询
        }
    }

    /**字段*/
    public function field($field = '*')
    {
        $this->Filed = $field;
        return $this;
    }

    public function  delete()
    {
        if($this->Where)
        {
            $sql = sprintf("delete from %s where %s", $this->Table_name, $this->Where);
        }
        else{
            $sql = sprintf("delete from %s ", $this->Table_name);
        }
        if ($this->Is_preview) {//如果是预览直接返回sql语句
            return $sql;
        } else {
            Model3::$last_sql = $sql;
            if(self::$record){//如果执行记录
                self::$alter_sql.=';'.$sql;
            }
            return MYSQL::execute($sql);//不是搜索的查询
        }
    }

    public function  insert($insert)
    {
        if (is_array($insert)) { //数组拼接执行
            $key_str = '';
            $value_str = '';
            //
            $link=MYSQL::getLink(false,1);//获取连接
            foreach ($insert as $key => $value) {
                $key = $this->filter($key);
                $value =mysqli_real_escape_string($link,$value);
                $key_str .= ',' . $key;
                //$value= str_replace("'",'’',$value);//替换单引号以免出问题
                $value_str .= ",'$value'";
            }

            $sql = sprintf("insert into %s(%s) VALUES (%s)", $this->Table_name, substr($key_str, 1), substr($value_str, 1));
            if ($this->Is_preview) {//如果是预览直接返回sql语句
                return $sql;
            } else {
                Model3::$last_sql = $sql;
                if(self::$record){//如果执行记录
                    self::$alter_sql.=';'.$sql;
                }
                $result=MYSQL::execute($sql);
            }
        }
        if (is_string($insert)) {//字符串直接执行
            $result= MYSQL::execute($insert);//不是搜索的查询
        }
        if($result){
           // return $this->getLastId();
            return MYSQL::insert_id();
            //return MYSQL::link->insert_id
        }else{
            return false;
        }
    }

    public function  getLastId()
    {
        return MYSQL::getLastId();
    }

//----------------------
    public function  table($name)
    {
        $prefix=MYSQL::$Link_arr['db_prefix'];

        $this->Table_name=$prefix . $name;//加上表前缀
        return $this;
    }

    public function preview()
    {
        $this->Is_preview = true;
        return $this;
    }

    public function  where($condition)
    {
        if (!$condition) {
            $this->Where = '1=1'; //默认选中全部
        } else if (is_string($condition)) {
            $this->Where = $condition;
        } else if (is_array($condition)) {
            //如果是多维数组则返回错误信息
            $str = '';
            $link=MYSQL::getLink(false,1);//获取连接
            foreach ($condition as $key => $value) {

                if(is_array($value)){ //条件必须是一维数组

                    throw new Exception(" Model2 sql condition must be One-dimensional array");
                }

                if(strstr($key,'PRE')){//如果键值包含PRE，直接连接value
                    $str.=$value;continue;//下一次循环
                }

                //链接两个条件默认用and
                $value_arr=explode(' ',$key);
                if (in_array('and' ,$value_arr) || in_array('or',$value_arr)||substr($str,strlen($str)-1,1)==='(') {
                    //如果存在直接往下执行
                } else {
                    $str .= ' and  ';
                }
                //过滤不合要求的value
                $value =mysqli_real_escape_string($link,$value);
                //$key中存在这些条件
                if (strstr($key, '=') || strstr($key, '<') || strstr($key, '>') || in_array('like',$value_arr)|| in_array('in',$value_arr)) {

                    if (in_array('in',$value_arr)) { //如果包含in方法
                        $value="'".str_replace(',',"','",$value)."'";//添加引号 lcc 2015-9-13

                        $str .= " $key ($value)";
                    }
                    else if (in_array('like',$value_arr)) { //如果包含like方法
                        $str .= " $key '%$value%'";
                    }
                    else {
                        $str .= " $key '$value'";
                    }
                } else if (is_numeric($key)) {
                    $str .=' '.$value;
                } else {//通过原来的方式来获取 现在默认是等于
                    $str .= " $key='$value'";
                }

            }
            //去掉开头的and
            if(substr($str,0,4)==' and') $str = substr($str, 4);
            $this->Where = $str;

        } else {//默认操作
            $this->Where = '1=2'; //不执行任何语句
        }
        return $this;
    }

    public function  limit($limit, $start = 0)
    {
        if ($limit) {
            $this->Condition['limit'] = $start ? "$start ,$limit" : $limit;
        }
        return $this;
    }

    public function  order($order)
    {
        if ($order) {
            $this->Condition['order'] = $order;
        }
        return $this;
    }

    public function  group($group)
    {
        $this->Condition['group'] = $group;
        return $this;
    }

    //过滤字段，防止sql注入  real_escape_string
    public function  filter($key)
    {
        if (!preg_match('/^[A-Z_\|\&\-.a-z0-9]+$/', trim($key))) {//只接收大小写和数字字符串
            write('非法sql注入:' . $key);
            throw_exception("illegal sql非法注入");
            exit(0);
        } else {
            return $key;
        }
    }

    public static function  get_last_sql()
    {
        return Model3::$last_sql;
    }
    public  static  function  M($name=''){
        return new Model3($name);
    }
    public  static  function  get_last_id(){
        return MYSQL::getLastId();
    }

     //添加获取数据表列表 主键  字段列表的功能
    //获取所有数据库中所有表
    public  static  function  get_table_list(){
   $sql="SELECT table_name FROM information_schema. TABLES WHERE table_schema = '".MYSQL::$Link_arr['db_name']."' AND table_type = 'base table'";
     $tables= MYSQL::query($sql);
        $new_arr=array();
        foreach($tables as $key=>$value){
            $new_arr[$key]=$value['table_name'];
        }
        return $new_arr;
    }
    //获取表所有字段  三种类型，0 返回原型 1 返回精简数组 2 返回字符串 ,最后一个参数决定是否使用表前缀
    public  static  function  get_field($table_name='',$type=''){
        $sql='SHOW COLUMNS FROM  '.$table_name;
        $columns=MYSQL::query($sql);
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
}

function Model3($table=''){
    return new Model3($table);
}
