<?php
/**
 * 模型基类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-3-20
 */
namespace framework\core;
use framework\database\Db;
use Templi;
use framework\cache\Cache;

class Model extends Object
{
    /** @var string 当前表明 */
    public $tableName ='';

    /** @var  DB 当前数据库连接对象*/
    protected $db;

    /** @var  \framework\cache\AbstractCache 缓存对象*/
    public $cache = null;

    /** @var array 所有数据库 */
    private static $_dbs = [];

    private $_where = '';
    private $_field = '';
    private $_order = '';
    private $_limit = '';       
    private $_set   = array();  //insert update操作数据
    private $_page  = array();  //分页配置数组
    private $_page_html = array();    //分页 html 代码
    //sql 语句
    private $_last_sql;

    /**
     * 构造函数
     * @param string $table 表名
     * @param int|string $dbSign 数据库唯一标识
     * @param array $config 数据库配置
     */
    function __construct($table='', $dbSign='master', $config = array())
    {
        $this->db($dbSign, $config);
        $this->cache = Templi::getApp()->getCache();
        $table && $this->tableName = $this->db->prefix.$table;
        $this->_restAllVar();
    }

    /**
     * 表名
     * return string
     */
    public function tableName()
    {
        return $this->tableName;
    }
    /**
     * 获取当前模型 的配置信息
     * 访问数据的的配置信息
     * 数据库表前缀 表名等
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->db->$name) ? $this->db->$name : parent::__get($name);
    }

    /**
     * 设置数据库连接 切换数据库
     * @param int|string $sign 数据库标识
     * @param array $config 数据库配置信息
     * @return $this
     */
    public function db($sign='master', $config=array())
    {
	    if (!isset(self::$_dbs[$sign])) {
            if(!$config){
                $config =  Templi::getApp()->getConfig("db.{$sign}");
            }
            $className = '\\framework\\database\\'.ucfirst($config['dbdrive']);
            self::$_dbs[$sign] = new $className($config);
        }
        if (isset(self::$_dbs[$sign])) {
            $this->db = self::$_dbs[$sign];
        } else {
            $this->db = self::$_dbs['master'];
        }
        return $this;
    }

    /**
     * 取数全部数据记录
     */
    public function fetch()
    {
        if (empty($this->_page_html)) {
           $result = $this->query($this->_select());
        } else {
            $result = $this->_page_html;
            $result['list'] = $this->query($this->_select());
        }
        $this->_restAllVar();
        return $result;
    }

    /**
     * 取一条数据记录
     *
     * @return array
     */
    public function fetchOne()
    {
        $result = $this->fetch();
        return isset($result[0]) ? $result[0] : [];
    }
    /**
     * 获取/设置缓存数据
     * @param string $cacheId
     * @return array
     */
    public function fetchCache($cacheId=null)
    {
        if (is_null($cacheId)) {
            $cacheId = substr(md5($this->_last_sql), 10);
        }
        $result = $this->cache->get($cacheId);
        if (is_null($cacheId) || empty($result)) {
            $result = $this->fetch();
            $this->cache->set($cacheId, $result);
        }
        return $result;
    }

    /**
     * 获取一条 缓存结果
     *
     * @param string $cacheId
     * @return array
     */
    public function fetchCacheOne($cacheId=null)
    {
        $result = $this->fetchCache($cacheId);
        return isset($result[0]) ? $result[0] : [];
    }
    /**
     * 设置操作数据表
     * 此方法不建议使用 （使用 此方法 会改变 之前模型 的操作表）
     * @param string $table
     * @return $this
     */
    public function table($table)
    {
        $this->tableName = $this->db->prefix.$table;
        return $this;
    }
    /**
     * 学则字段字段
     * @param string $field
     *
     * @return $this
     */
    public function field($field)
    {
        //不选字段默认为 * 防止用户输入*
        if (trim($field) == '*') {
            return $this;
        }
        if (is_array($field)) {
            $fields = implode(',', array_map(array($this, 'addSpecialChar'), $field));
        } else {
            $fields = explode(',', $field);
            array_walk($fields, array($this, 'addSpecialChar'));
            $fields = implode(',', $fields); 
        }
        $this->_field && $this->_field .= ', ';
        $this->_field .= $fields;
        return $this;
    }

    /**
     * where 条件
     * @param array|string $where
     * @param string $joint $where数组内个元素之间的关系($where 为数组时有效)
     * @param string $compare
     * @return $this
     */
    public function where($where, $compare = '=', $joint='AND')
    {
        $sql = $this->_where($where, $joint, $compare);
        if($this->_where){
            if(count($where)){
                $this->_where .= ' AND ('. $sql .')';
            }else{
                $this->_where .= $sql;
            }
        }
        return $this;
    }

    /**
     * where 条件 与现有条件or 的关系
     * @param array|string $where
     * @param string $joint $where 数组内个元素之间的关系($where 为数组时有效)
     * @param string $compare 字段的筛选方式
     * @return $this
     */
    public function whereOr($where, $compare = '=', $joint='AND')
    {
        $sql = $this->_where($where, $joint, $compare);
        if($this->_where){
            if(count($where)>1){
                $this->_where .= ' OR ('.$sql.')';
            }else{
                $this->_where .= $sql;
            }
        }
        return $this;
    }

    /**
     * 给 update insert 赋值
     * @param array $data
     * @throws Abnormal
     * @return $this
     */
    public function set(array $data)
    {
        $this->_set = array_merge($this->_set, $data);
        return $this;
    }

    /**
     * oder by
     * @param string $order
     *
     * example id desc
     * @return $this
     */
    public function order($order)
    {
        if (is_array($order)) {
            foreach ($order as $key =>$val){
                $this->_order && $this->_order .= ', ';
                $this->_order .= $this->addSpecialChar($key).' '.$val;
            }
        } else {
            $this->_order && $this->_order .= ', ';
            $this->_order .= $order;
        }
        return $this;
    }

    /**
     * limit
     * $limit example 0,20* example 0,20
     *
     * @param $listNum
     * @param null $offset
     * @return $this
     */
    public function limit($listNum, $offset = NULL)
    {
        if($offset != NULL){
            $this->_limit = " $offset,$listNum";
        }else{
            $this->_limit = $listNum;
        }
        return $this;
    }

    /**
     * 分页设置
     * @param array $page
     * $page['total'] 总数
     * $page['listNum'] 每页显示条数
     * $page['current_page'] 当前页
     * $page['pageNum'] 每页显示的 页码数
     * $page['urlrule'] 分页 url 规则
     * $page['maxpage'] 最大页数
     * @return $this
     */
    public function page($page)
    {
        if (is_array($page)) {
            foreach ($this->_page as $key =>$val) {
                if (!is_null($page[$key])) {
                    $this->_page[$key] = $page[$key];
                }
            }
        } else {
            $this->_page['current_page'] = $page;
        }
        $page = new \framework\libraries\Page($this->_page);
        if (empty($this->_limit) && $this->_page['listNum']) {
            $this->limit($this->_page['listNum'], $page->offset);
        }
        $this->_page_html['page_thml'] = $page->pageHtml();
        $this->_page_html['total'] =  $this->count($this->_where);
        return $this;
    }

    /**
     * 多条查询并分页
     * @param null $where 条件语句 可以为数组
     * @param string $field 字段
     * @param string $order 排序
     * @param int $current_page 当前页
     * @param int $listNum 每页显示条数
     * @param int $pageNum 每页显示的 页码数
     * @param string $urlrule url 规则
     * @param int $maxpage 最多显示页数
     *
     * @return $this
     */
    public function getList(
        $where=NULL, $field=NULL, $order=NULL, $current_page=NULL,
        $listNum=NULL, $pageNum=NULL, $urlrule=NULL, $maxpage=NULL
    ) {
        $this->select($where, $field, $order);
        //分页
        $this->page(array(
            'current_page'  =>  $current_page,
            'listNum'       =>  $listNum,
            'pageNum'       =>  $pageNum,
            'urlrule'       =>  $urlrule,
            'maxpage'       =>  $maxpage
        ));

        return $this;
    }

    /**
     * 多条查询
     * @param array $where 条件语句 可以为数组
     * @param string $field 字段
     * @param string $order 排序
     * @param string $limit 条数限制
     *
     * @return $this
     */
    public function select($where=NULL, $field=NULL, $order=NULL, $limit=NULL)
    {
        $where && $this->where($where);
        $field && $this->field($field);
        $order && $this->order($order);
        $limit && $this->limit($limit);

        return $this;
    }

    /**
     * 生成 select sql 语句
     */
    private function _select()
    {
        $sql  = 'SELECT '.$this->_field.' FROM `'.$this->tableName.'`';
        $sql .= $this->_where?' WHERE '.$this->_where:'';
        $sql .= $this->_order?' ORDER BY '.$this->_order:'';
        $sql .= $this->_limit? ' LIMIT '. $this->_limit : '';
        return $sql;
    }

    /**
     * 查询数据条数
     * @param array|string 查询条件 可以是 数组
     * @return mixed
     */
    public function count($where = NULL)
    {
        $where && $this->where($where);
        
        $sql = 'SELECT COUNT(*) AS `num` FROM '.$this->tableName;
        $sql .= $where?' where '.$this->_where:'';
        $sql .=' limit 1';
        $res = $this->query($sql);
        return isset($res[0]['num'])?$res[0]['num']:$res;
    }

    /**
     * 修改数据
     * @param mixed $data 要修改的数据 字符串为 sql 语句 数组key 为字段名 value 为字段值
     * @param mixed $where 条件语句 可为数组
     *
     * @return bool
     */
    public function update($data = NULL, $where=NULL)
    {
        $data && $this->set($data);
        $where && $this->where($where);
        
        if(empty($this->_where) || empty($this->_set)) {
             return false;
        }
        $fields = array();
        foreach($this->_set as $k => $v){
            switch(substr($v, 0, 2)){
                case '+=':
                    $v= substr($v,2);
                    if(is_numeric($v)){
                        $fields[] =$this->addSpecialChar($k).'='.$this->addSpecialChar($k).'+'.$this->escapeString($v, false);
                    }else{
                        continue;
                    }
                    break;
                case '-=':
                    $v= substr($v,2);
                    if(is_numeric($v)){
                        $fields[] =$this->addSpecialChar($k).'='.$this->addSpecialChar($k).'-'.$this->escapeString($v, false);

                    }else{
                        continue;
                    }
                    break;
                default:
                    $fields[]=$this->addSpecialChar($k).'='.$this->escapeString($v );
                    break;
            }
        }
        if(empty($fields)){
            return false;
        }
        $field = implode(',', $fields);
        
        $sql ='UPDATE `'.$this->tableName.'` SET '.$field.' WHERE '.$this->_where;
        return $this->query($sql);
    }

    /**
     * 插入数据
     * @param array $data 要添加的数据 key 为字段名 value 为字段值
     * @param bool $return_insert_id 是否返回主键 号
     * @param bool $replace 是否 为替换插入
     * @return bool
     */
    public function insert($data = NULL, $return_insert_id = false, $replace = false)
    {
        $data && $this->set($data);
        
        if(!is_array($this->_set) || count($this->_set) == 0){
            return false;
        } 
        $fields = array_keys($this->_set);
        $values = array_values($this->_set);
        $fields = implode(',',array_map(array($this, 'addSpecialChar'), $fields));
        $values = implode(',',array_map(array($this, 'escapeString'), $values));

        $sql  = $replace?'REPLACE INTO ':'INSERT INTO ';
        $sql .= $this->tableName. '('.$fields.') VALUES ('.$values.')';
        $result = $this->query($sql);
        return $return_insert_id?$this->db->insertId():$result;
    }
    /**
      * 删除数据 
      * @param string|array $where 条件
      * @return bool
      */
    public function delete($where=NULL)
    {
        $where && $this->where($where);
        if(!$this->_where) {
            return false;
        }
        $sql = 'DELETE FROM ' .$this->tableName. ' WHERE '.$this->_where;
        return $this->query($sql);
    }
    /**
     * 执行基本的 mysql查询
     * @param string $sql
     * @return mixed
     */
    public function query($sql)
    {
        $this->_last_sql = trim($sql);
        if (strtoupper(substr($this->_last_sql, 0, 6)) == 'SELECT') {
            return $this->db->query($this->_last_sql);
        } else {
            return $this->db->execute($this->_last_sql);
        }
    }

    /**
     * 对字段两边加反引号，以保证数据库安全
     * @param string $value 数组值
     * @return string
     */
    public function addSpecialChar(&$value)
    {
        if('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos ( $value, '`')) {
            //不处理包含* 或者 使用了sql方法。
        } else {
            $value = '`'.trim($value).'`';
        }
        return $value;
    }

    /**
     * 对字段值两边加引号转义特殊字符，以保证数据库安全
     * @param string $value 数组值
     * @param bool $quotation 是否添加引号
     * @return string
     */
    public function escapeString(&$value, $quotation=true)
    {
        if ($quotation) {
                $q = '\'';
        } else {
                $q = '';
        }
        $value = $q.addslashes($value).$q;
        return $value;
    }
    /**
     * 获取最后一次执行的sql 语句
     */
    public function lastSql(){
        return $this->_last_sql;
    }

    /**
     * 将数组转换为SQL语句
     * @param array $where 要生成的数组
     * @param string $font 连接串
     * @param string $compare 比较字符 (=,!=,in not in, like)
     * @throws Abnormal
     * @return string
     */
    protected function _where($where, $font = ' AND ', $compare = '=')
    {
        $sql = '';
        if (is_array($where)) {
            $compare = strtoupper(trim($compare));
            $allowed = array('=', '>=', '<=', '>', '<', '<>', '!=', 'LIKE');
            if (!in_array($compare ,$allowed)) {
                throw new Abnormal('不支持的比较操作符'.$compare, 500);
            }
            foreach ($where as $key=>$val) {
                $sql && $sql .= ' '.$font ;
                $sql .= ' '.$this->addSpecialChar($key). $compare .$this->escapeString($val, true);
            }
        } else {
            $sql && $sql .= ' '.$font;
            $sql .= ' '.$where;
        }
        return $sql;
    }
    /**
     * 重置 类属性
     */
    private function _restAllVar()
    {
        $this->_where = '';
        $this->_field = '*';
        $this->_order = '';
        $this->_limit = '';
        $this->_set   = array();
        $this->_page  = array(
            'total'=>0,
            'current_page'=>1, 
            'pageNum'=>8, 
            'listNum'=>20, 
            'urlrule'=>'',
            'maxpage'=>0
            );
        $this->_page_html = '';
    }
}