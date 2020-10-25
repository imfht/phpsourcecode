<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/6/17 0017
 * Time: 14:57
 * //查询可以一起查询，写入需要一起写入
 */
class SQLIT {
    private  static $link='';
    private  static  $transaction=false;
    public   static  $Link_arr=array();

    //设置链接优化 切换连接时关闭原来的连接（如果存在的话） 设置的是$Link_arr
    public  static  function  set_link($link=array()){

        if(self::$Link_arr['filename']!=$link['filename']&&is_object(self::$link)){
            //如果文件路径相同说明是同一个数据库，不需要关闭即可
            self::$link->close();//关闭原来的连接
            self::$link=null;
        }
        SQLIT::$Link_arr=$link;  //如果文件不同才重新设置，说明数据库不同
    }
    //打开其它数据库
    public  static function  open($filename){
        self::$link=new SQLite3($filename);
    }
    //连接数据库，如果没有连接的话
    public  static  function connect($config=array()){
        if($config){
            $filename=$config['filename'];
        }
        else{
            $filename=self::$Link_arr['filename'];
        }

        if(!file_exists($filename)){
            exit('sqlit db file not exist:'.$filename);
        }
        self::$link=new SQLite3($filename);
        self::$link->busyTimeout(2000); //1000毫秒,当等待1秒还没操作完就报错

    }

    //执行增改删
    public  static  function execute($sql){

        if(!is_object(self::$link)){self::connect();}

        //如果是事务不立即执行而是拼接sql语句
        if(self::$transaction){
            self::$transaction.=' '.$sql.'; ';
        }
        else{
            $query = self::$link->exec($sql);

            if ($query === false){
                $error = 'Db Error: '.self::$link->lastErrorMsg();
                exit($error.'<br/>'.$sql);
            }else {
                return $query; //没出错就证明正常
            }
        }
    }
    //输出堆栈信息
    public static  function  print_stack(){
        header("Content-type: text/html; charset=utf-8");
        $array =debug_backtrace();
//print_r($array);//信息很齐全
        unset($array[0]);
        $html ='';
        foreach($array as $row)
        {
            $html .=$row['file'].':'.$row['line'].'行,调用方法:'.$row['function']."<p>";
        }
        echo $html;
    }
    //查询
    public  static  function  query($sql){

        if(!is_object(self::$link)){self::connect();}//还没有连接过使用默认链接函数连接

        $result = self::$link->query($sql);
        if ($result === false){
            $error_str="Db Error: ".self::$link->lastErrorMsg();
            $last_sql='<br/><br/>lastSql: '.$sql;
            echo $error_str.$last_sql.'<br/> '.self::print_stack();
            return false;
        }
        $array=array();
        while ($tmp=$result->fetchArray(SQLITE3_ASSOC)){
            $array[] = $tmp;
        }

        return $array;
    }
    //获取最后的id
    public  static function getLastId(){
        if(!is_object(self::$link)){self::connect();}//还没有连接过使用默认链接函数连接
        $id = self::$link->lastInsertRowID();  //如果没有查询到尝试使用$link->changes()方法
        return $id;
    }
    //sqlit支持多项一起写入，减少数据打开次数
   //设置标识，commit之前的修改语句不会立即执行，直到commit执行 ，当一条语句不成功，所有都不会执行
    public  static  function  begin(){
       self::$transaction='begin; ';
    }
    //提交事务
    public  static  function  commit(){
        $sql=self::$transaction.' commit;';
        self::$transaction=false;

      return  self::execute($sql);  //返回事务执行结果
    }



}



//sqlit数据库
class ModelLit {

    protected static $last_sql;
    protected $Table_name = '';
    protected $Where = '';
    protected $Filed = '*';
    protected $Condition = array('limit' => '', 'order' => '', 'group' => '');
    protected $Is_preview = false;//是否是sql预览



    //设置链接
    public  static  function  set_link($db_path='',$db_prefix=''){
         //如果不存在数据库文件则创建
           if(!file_exists($db_path)){
               new SQLite3($db_path); //没有则创建数据库
           }
        SQLIT::set_link(array(
            'filename'=>$db_path,
            'db_prefix'=>$db_prefix
        ));

    }
    //构造函数
    public function __construct($name = '')
    {
        $this->Table_name =SQLIT::$Link_arr['db_prefix'].$name;
    }

    //查询操作
    public function  query($sql)
    {
        return SQLIT::query($sql);
    }

    //执行操作
    public  function   execute($sql)
    {
        return SQLIT::execute($this->format_sql($sql));//不是搜索的查询
    }

  //开始事务
    public  static  function  begin(){
   SQLIT::begin();
    }
    //提交事务
    public  static  function  commit(){

   SQLIT::commit();
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
            ModelLit::$last_sql = $sql;
            return SQLIT::query($sql);//不是搜索的查询
        }
    }

    /**仅查询第一条数据*/
    public function  find()
    {
        $ret=$this->limit(1)->select();
        return $ret[0];
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
            return $result[0]['num'];
        }
    }
    //将字符串里的#替换为表前缀
    private  function  format_sql($sql){
        $prefix=SQLIT::$Link_arr['db_prefix'];
        return str_replace('#',$prefix,$sql);
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
            foreach ($update as $key => $value) {

                if(strstr($key,'PRE')){//如果键值包含PRE，直接连接value
                    $str_arr=explode(' ',$key);
                    if(count($str_arr)>1){
                        $str.=','.$str_arr[0].'='.$value;
                    }
                    else{
                        $str .= ",$key='$value'";
                    }
                    //  $str.=$value;continue;//下一次循环
                }
                else{
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
            ModelLit::$last_sql = $sql;
            return SQLIT::execute($this->format_sql($sql));//不是搜索的查询
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
            ModelLit::$last_sql = $sql;
            return SQLIT::execute($sql);//不是搜索的查询
        }
    }
    //插入数据
    public function  insert($insert)
    {
        if (is_array($insert)) { //数组拼接执行
            $key_str = '';
            $value_str = '';
            foreach ($insert as $key => $value) {
                $key = $this->filter($key);

                //对单引号转义
                $value = str_replace("'","''",$value);
                $key_str .= ',' . $key;
                $value_str .= ",'$value'";
            }

            $sql = sprintf("insert into %s(%s) VALUES (%s)", $this->Table_name, substr($key_str, 1), substr($value_str, 1));

            if ($this->Is_preview) {//如果是预览直接返回sql语句
                return $sql;
            } else {
                ModelLit::$last_sql = $sql;
                $result=SQLIT::execute($sql);
            }
        }

        if (is_string($insert)) {//字符串直接执行
            $result= SQLIT::execute($insert);//不是搜索的查询
        }
        if($result){
            return $this->getLastId();
        }else{
            return false;
        }
    }

    public function  getLastId()
    {
        return SQLIT::getLastId();
    }

//----------------------
    public function  table($name)
    {
        $prefix=SQLIT::$Link_arr['db_prefix'];

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
            foreach ($condition as $key => $value) {

                if(is_array($value)){ //条件必须是一维数组

                    throw new Exception(" ModelLit sql condition must be One-dimensional array");
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

    //过滤字段，防止sql注入
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
        return ModelLit::$last_sql;
    }
    public  static  function  M($name=''){
        return new ModelLit($name);
    }
    public  static  function  get_last_id(){
        return SQLIT::getLastId();
    }


}

//产生一个新的模型
function ModelLit($table=''){
    return new ModelLit($table);
}
