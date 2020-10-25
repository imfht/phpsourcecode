<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 基类扩展-模型
 * 
 * @author 牧羊人
 * @date 2018-07-11
 */
namespace Common\Model;
use Think\Think;
use Admin\Model\AdminModel;
class CBaseModel extends BaseModel {
    function __construct($table) {
        parent::__construct($table);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function edit($data, &$error='',$is_sql=false) {
        $id = (int)$data['id'];
        if($id) {
            if(empty($data['upd_time'])) {
                $data['upd_time'] = time();
            }
            if (empty($data['upd_user'])) {
                $data['upd_user'] = (int)$_SESSION['adminId'];
            }
        } else {
            if(empty($data['add_time'])) {
                $data['add_time'] = time();
            }
            if (empty($data['add_user'])) {
                $data['add_user'] = (int)$_SESSION['adminId'];
            }
        }
        
        //格式化表数据
        $this->formatData($data, $id);
        
        //数据表验证
        if(!$this->create($data)) {
            $error = $this->getError();
            return 0;
        }
        
        //数据入库处理
        if($id) {
            //修改数据
            $result = $this->where("id={$id}")->save($data);
            $rowId = $id;
            if($is_sql) echo $this->_sql();
            
        }else{
            //新增数据
            $result = $this->add($data);
            $rowId = $result;
            if($is_sql) echo $this->_sql();
            
        }
        if($result!==false) {
            //重置缓存
            $data['id'] = $rowId;
            $this->_cacheReset($rowId, $data, $id);
        }
        return $rowId;
        
    }
    
    /**
     * 格式化编辑的数据
     * 
     * @author 牧羊人
     * @date 2018-08-30
     * @param $data 要格式话的数据
     * @param number $id 编号
     * @param string $table 带前缀的表名
     * @return multitype:multitype:Ambigous <number, string, Ambigous <string, Ambigous <number, unknown>>>
     */
    public function formatData(&$data, $id=0, $table="") {
        $dataList = array();
        $tables = $table ? explode(",", $table) : array($this->getTableName());
        $newData = array();
        foreach ($tables as $table) {
            $tempData = array();
            $fieldInfoList = $this->getFieldInfoList($table);
            foreach ($fieldInfoList as $field=>$fieldInfo) {
                if ($field == "id") continue;
                //对强制
                if (isset($data[$field])) {
                    if ($fieldInfo['type']=="int") {
                        $newData[$field] = (int) $data[$field];
                    } else {
                        $newData[$field] = (string) $data[$field];
                    }
                }
                if (!isset($data[$field]) && in_array($field, array('upd_time','add_time'))) {
                    continue;
                }
                //插入数据-设置默认值
                if (!$id && !isset($data[$field])) {
                    $newData[$field] = $fieldInfo['default'];
                }
                if (isset($newData[$field])) {
                    $tempData[$field] = $newData[$field];
                }
            }
            $dataList[] = $tempData;
        }
        $data = $newData;
        return $dataList;
    }
    
    /**
     * 获取字段信息列表 
     * 
     * @author 牧羊人
     * @date 2018-08-30
     */
    public function getFieldInfoList($table="") {
        $table = $table ? $table : $this->getTableName();
        $fieldList = $this->query("SHOW FIELDS FROM {$table}");
        $infoList = array();
        foreach ($fieldList as $row) {
            if ((strpos($row['type'], "int") === false) || (strpos($row['type'], "bigint") !== false)) {
                $type = "string";
                $default = $row['default'] ? $row['default'] : "";
            } else {
                $type = "int";
                $default = $row['default'] ? $row['default'] : 0;
            }
            $infoList[$row['field']] = array(
                'type'=>$type,
                'default'=>$default
            );
        }
        return $infoList;
    }
    
    /**
     * 获取信息
     * 
     * @author 牧羊人
     * @date 2018-07-10
     */
    public function getInfo($id,$flag=false) {
        $info = $this->getFuncCache("info", $id);
        if($info) {
            
            //添加时间
            if(isset($info['add_time']) && $info['add_time']) {
                $info['format_add_time'] = date('Y-m-d H:i:s',$info['add_time']);
            }
            
            //更新时间
            if(isset($info['upd_time']) && $info['upd_time']) {
                $info['format_upd_time'] = date('Y-m-d H:i:s',$info['upd_time']);
            }
            
            //获取系统操作人信息
            if($flag) {
                
                //添加人
                if($info['add_user']) {
                    $info['format_add_user'] = $this->getSystemAdminName($info['add_user']);
                }
                
                //更新人
                if($info['upd_user']) {
                    $info['format_upd_user'] = $this->getSystemAdminName($info['upd_user']);
                }
                
            }
            
        }
        return $info;
    } 
    
    /**
     * 获取系统操作人名称
     * 
     * @author 牧羊人
     * @date 2018-08-16
     */
    public function getSystemAdminName($adminId) {
        if(!$adminId) return '';
        $adminMod = new AdminModel();
        $adminList = $adminMod->getAll();
        return $adminList[$adminId]['realname'];
    }
    
    /**
     * 获取单条数据
     * 
     * @author 牧羊人
     * @date 2018-08-30
     */
    public function getRowByAttr($map=[], $fields="*", $id=false) {
        //查询条件
        if(is_array($map)) {
            $map['mark'] = 1;
            if($id) {
                $map['id'] = array('neq',$id);
            }
        }else{
            $map .= " AND mark=1 ";
            if($id) {
                $map .= " AND id!={$id} ";
            }
        }
        $info = $this->field($fields)->where($map)->find();
        return $info;
    }
    
    /**
     * 获取总数
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function getCount($map=[],$is_sql=false) {
        //查询条件
        if(is_array($map)) {
            $map['mark'] = 1;
        }else{
            $map .= " AND mark=1 ";
        }
        $count = $this->where($map)->count();
        if($is_sql) echo $this->_sql();
        return (int)$count;
    }
    
    /**
     * 计算总和
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function getSum($map=[],$field,$is_sql=false) {
        //查询条件
        if(is_array($map)) {
            $map['mark'] = 1;
        }else{
            $map .= " AND mark=1 ";
        }
        $result = $this->where($map)->sum($field);
        if($is_sql) echo $this->_sql();
        return $result;
    }
    
    /**
     * 获取分页数据
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function pageData() {
        
    }
    
    /**
     * 通用删除方法【物理删除】
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    public function drop($id,$is_sql=false){
        //$rs = $this->delete($id);
        $result = $this->where("id={$id}")->setField('mark','0');
        if($is_sql) echo $this->_sql();
        if($result!==false) {
            //删除成功
            $this->_cacheDelete($id);
        }
        return $result;
    }
    
}