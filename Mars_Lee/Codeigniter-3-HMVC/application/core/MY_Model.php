<?php
/**
 * Class MY_Model
 *
 * 视图加载控制器，需要显示的页面需加载此控制器
 * Created by PhpStorm.
 * User: li
 * Date: 15-10-9
 * Time: 下午7:19
 * @property CI_DB_active_record $db
 */
class MY_Model extends CI_Model
{
    protected $_table_name;

    protected $_condition_fields = array();

    protected $_CI;

    public function __construct()
    {
        parent::__construct();

        $this->_table_name = $this->db->dbprefix . $this->_table_name;

        $this->_CI = & get_instance();
    }

    public function query()
    {

    }

    /**
     * 根据id获取一条记录
     *
     * @param $id 表id
     */
    public function get($id)
    {
        $query = $this->db->where('id', $id)->get($this->_table_name);
        return $query->row_array();
    }

    public function set_increment($field,$id,$num){
        $this->db->set($field,$field.'+'.intval($num),false);
        $this->db->where('id',$id);
        $this->db->update($this->_table_name);
        return $this->db->affected_rows()> 0;
    }

    /**
     * @param $ids
     * @param null $field
     * @return mixed
     */
    public function get_in($ids,$field=NULL){
        if($field!==NULL){
            $this->db->select($field);
        }
        $query =$this->db->where_in('id',$ids)->get($this->_table_name);
        return $query->result_array();
    }




    /**
     * 根据条件获取一条记录
     *
     * @param array $conditions 条件
     *
     * @return array
     */
    public function getOne($conditions, $order = '')
    {
        $this->parse_conditions($conditions);
        if ($order != '') {
            $this->db->order_by($order);
        }
        $this->db->limit(1);
        return $this->db->get($this->_table_name)->row_array();

    }

    //public function get

    /**
     * 插入记录
     *
     * @param array $data 插入数据
     *
     * @return int 成功返回id，失败返回0
     * @throws Exception
     */
    public function insert($data)
    {
        unset($data['id']);
        $insert_res = $this->db->insert($this->_table_name, $data);
        if($insert_res === FALSE)
        {
            throw new Exception('db insert error.');
        }
        return $this->db->affected_rows() ? $this->db->insert_id() : 0;
    }

    /**
     * 更新记录
     *
     * @param array $data 修改数据
     * @param int   $id   可选，如果为空，$data['id']必须有
     *
     * @return int 返回id
     * @throws Exception
     */
    public function update($data, $id = NULL)
    {
        if (NULL === $id)
        {
            if (isset($data['id']))
            {
                $id = $data['id'];
                //unset($data['id']);
            }
            else
            {
                throw new Exception('缺少id');
            }
        }

        if (intval($id) <= 0)
        {
            throw new Exception('id必须大于0');
        }

        $result = $this->db->where('id', $id)->limit(1)->update($this->_table_name, $data);

        if ($result === FALSE)
        {
            throw new Exception('db update error.');
        }

        return $id;
    }

    /**
     * 保存记录
     *
     * @param string $data 保存数据
     *
     * @return void
     */
    public function save($data)
    {
        if (isset($data['id']) && intval($data['id']) > 0)
        {
            return $this->update($data, $data['id']);
        }
        else
        {
            return $this->insert($data);
        }
    }

    /**
     * 删除记录
     *
     * @param string $id 删除id
     *
     * @return void
     * @throws Exception
     */
    public function delete($id)
    {
        if (intval($id) <= 0)
        {
            throw new Exception('id必须大于0');
        }

        $result = $this->db->where('id', $id)->limit(1)->delete($this->_table_name);
        if ($result === FALSE)
        {
            throw new Exception('db delete error.');
        }
        return $result;
    }

    /**
     * 根据条件删除多行记录
     *
     * @param array $conditions 可选，如果为空，删除表中所有数据
     *
     * @return boolean 返回是否删除成功
     */
    public function batch_delete($conditions = array())
    {
        if (empty($conditions))
        {
            throw new Exception('请传入需要删除的条件.');
        }
        $this->parse_conditions($conditions);
        $result = $this->db->delete($this->_table_name);
        if ($result === FALSE)
        {
            throw new Exception('db batch delete error.');
        }
        return $result;
    }

    /**
     * 软删除
     *
     * @param int $id
     */
    public function soft_delete($id)
    {
        $data = array(
            'id' => $id,
            'is_del' => 1,
        );

        return $this->save($data);;
    }
    /**
     * 撤销删除
     *
     * @param int $id
     */
    public function reback($id)
    {
        $data = array(
            'id' => $id,
            'is_del' => 0,
        );

        return $this->save($data);;
    }
    /**
     * 取符合条件的记录
     *
     * @param array  $conditions 查询条件
     * @param string $fields     查询字段
     * @param string $options    分页/不分页、排序
     *
     * @return array
     */
    public function fetch_array($conditions = array(), $fields = '*', $options = array())
    {
        if(is_array($fields)){ //第二个参数 false 不添加反引号保护字段
            $this->db->select($fields[0], $fields[1]);
        }else{
            $this->db->select($fields);
        }
        $this->parse_conditions($conditions);
        if (isset($options['limit']))
        {
            // 生成SQL: LIMIT $options['limit'][1], $options['limit'][0]
            $this->db->limit($options['limit'][0], $options['limit'][1]);
        }
        /*else
        {
            if (!isset($options['no_limit']))
            {
                $this->db->limit(20);
            }
        }*/

        if (isset($options['order']))
        {
            foreach ($options['order'] as $order)
            {
                if(is_array($order) && 2 <= count($order))
                {
                    //$order[0]:字段名
                    //$order[1]:排序方式 asc、desc、random
                    $this->db->order_by($order[0], $order[1]);
                }else{//传入一个参数 字符串 例：$this->db->order_by('title desc, name asc');
                    $this->db->order_by($order);
                }
            }
        }

        return $this->db->get($this->_table_name)->result_array();
    }

    /**
     * 取满足条件的记录数
     *
     * @param array $conditions 查询条件
     *
     * @return int
     */
    public function fetch_count($conditions = array())
    {
        $this->parse_conditions($conditions);

        return $this->db->count_all_results($this->_table_name);
    }

    /**
     * 批量保存
     *
     * @param array $datas 需要保存的数据
     *
     * @return void
     */
    public function batch_save($datas)
    {
        return self::batch_insert($datas);
    }

    /**
     * 更新多行记录
     *
     * @param array $data       需要修改数据
     * @param array $conditions 可选，如果为空，更新所有数据
     *
     * @return int 返回影响行数
     * @throws Exception
     */
    public function batch_update($data, $conditions = array())
    {
        if (empty($data))
        {
            throw new Exception('缺少需要更新字段');
        }
        $this->parse_conditions($conditions);
        $result = $this->db->update($this->_table_name, $data);
        if ($result === FALSE)
        {
            throw new Exception('db update by condition error.');
        }
        return $result;
    }

    /**
     * 批量插入
     *
     * @param array $data 批量插入数据
     *
     * @return bool
     * @throws Exception
     */
    public function batch_insert($data)
    {
        //return $this->db->insert_batch($this->_table_name, $data);
        if (!empty($data)) {
            $this->_db_insert_batch($data);
        } else {
            throw new Exception('请传入需要保存的数据.');
        }

    }

    /**
     * ci的activerecod的insert_batch没有返回值 有可能部分执行错误
     *
     * @param array $data insert字段
     *
     * @return void
     * @throws Exception
     */
    private function _db_insert_batch($data)
    {
        $db_actv = $this->db->set_insert_batch($data);

        if (count($db_actv->ar_set) == 0) {
            throw new Exception('db_must_use_set');
        }

        for ($i = 0, $total = count($db_actv->ar_set); $i < $total; $i = $i + 100)
        {
            $table = $this->db->_protect_identifiers($this->_table_name, TRUE, NULL, FALSE);
            $keys = $db_actv->ar_keys;
            $values = array_slice($db_actv->ar_set, $i, 100);

            $sql = "INSERT INTO " . $table . " (" . implode(', ', $keys) . ") VALUES " . implode(', ', $values);

            $batch_sql_status = $this->db->query($sql);
            if ($batch_sql_status === FALSE) {
                throw new Exception('db batch insert error.');
            }
        }

        $this->_reset_write($this->db);
    }

    /**
     * 每个model字定义的条件解析
     *
     * @param array $conditions
     *
     * @return array
     */
    protected function self_conditions(array $conditions)
    {
        //使用完的条件unset
        return $conditions;
    }

    /**
     * 把条件数组转转化成sql条件
     *
     * @param $conditions
     */
    protected function parse_conditions(array $conditions)
    {
        $conditions = $this->self_conditions($conditions);

        if (is_array($conditions) && count($conditions) > 0) {
            foreach ($conditions as $field => $value) {
                if ($field == 'ids') {
                    $this->db->where_in('id', $value);
                } else {
                    $this->db->where($this->_table_name.'.'.$field, $value);
                }
            }
        }
        //默认添加的查询条件
        foreach ($this->_condition_fields as $field => $value) {
            if (isset($conditions[$field])) {
                continue;
            }
            $this->db->where($field, $value);
        }
    }

    /**
     * 设置类的属性
     *
     * @param string $name  class var
     *
     * @param mixed  $value value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * 清上次执行数据
     *
     * @param type $db_actv
     *
     * @return void
     */
    private function _reset_write($db_actv)
    {
        $ar_reset_items = array(
            'ar_set' => array(),
            'ar_from' => array(),
            'ar_where' => array(),
            'ar_like' => array(),
            'ar_orderby' => array(),
            'ar_keys' => array(),
            'ar_limit' => FALSE,
            'ar_order' => FALSE
        );
        foreach ($ar_reset_items as $item => $default_value)
        {
            if (!in_array($item, $db_actv->ar_store_array)) {
                $db_actv->$item = $default_value;
            }
        }
    }

    public function pagination
    (
        $base_url, $page = 1, $conditions = array(), $order = '', $fields = '*', $per_page = 20
    ) {
        $this->_CI->load->library('pagination');

        $total_rows = $this->fetch_count($conditions);

        $config = array();
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $this->_CI->pagination->initialize($config);
        $page_html = $this->_CI->pagination->create_links();

        $start = ($page - 1)  * $per_page;
        $options = array(
            'limit' => array($per_page, $start),
        );

        if ($order) {
            $options['order'] = array($order);
        }
        $list = $this->fetch_array($conditions, $fields, $options);

        return array('list' => $list, 'page_html' => $page_html);
    }

    public function pagination_join( $base_url, $page = 1, $conditions = array(), $order = '', $fields = '*', $per_page = 20,$join_conditions){
        $this->_CI->load->library('pagination');

        $total_rows = $this->fetch_count($conditions);

        $config = array();
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $this->_CI->pagination->initialize($config);
        $page_html = $this->_CI->pagination->create_links();

        $start = ($page - 1)  * $per_page;
        $options = array(
            'limit' => array($per_page, $start),
        );

        if ($order) {
            $options['order'] = array($order);
        }
        $list = $this->join_table_fetch_array($conditions, $fields, $options,$join_conditions);

        return array('list' => $list, 'page_html' => $page_html);
    }

    function join_table_fetch_array($conditions, $fields, $options,$join_conditions){
        if(is_array($fields)){ //第二个参数 false 不添加反引号保护字段
            $this->db->select($fields[0], $fields[1]);
        }else{
            $this->db->select($fields);
        }
        $this->parse_conditions($conditions);
        if (isset($options['limit']))
        {
            $this->db->limit($options['limit'][0], $options['limit'][1]);
        }

        if (isset($options['order']))
        {
            foreach ($options['order'] as $order)
            {
                if(is_array($order) && 2 <= count($order))
                {
                    $this->db->order_by($order[0], $order[1]);
                }else{
                    $this->db->order_by($order);
                }
            }
        }
        if(is_array($join_conditions) && count($join_conditions)>0){
            foreach($join_conditions as $v){
                $this->db->join($v['table_name'], $v['condition'],'left');
                if(isset($v['field']))
                    $this->db->select($v['table_name'].'.'.$v['field']);
            }
        }
        return $this->db->get($this->_table_name)->result_array();
    }
}