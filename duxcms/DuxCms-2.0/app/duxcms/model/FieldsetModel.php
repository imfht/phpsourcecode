<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 字段集操作
 */
class FieldsetModel extends BaseModel {

    //验证
    protected $_validate = array(
        array('name','1,200', '名称只能为1~200个字符', 1 ,'length'),
        array('table', '', '已存在相同的数据表', 1, 'unique'),
    );

    /**
     * 获取信息
     * @param int $fieldsetId ID
     * @return array 信息
     */
    public function getInfo($fieldsetId)
    {
        $map = array();
        $map['fieldset_id'] = $fieldsetId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @param bool $autoKey 自动更新主键
     * @return bool 更新状态
     */
    public function saveData($type = 'add' ,$autoKey = false){
        $data = $this->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            //创建数据表
            $sql=" CREATE TABLE IF NOT EXISTS `{pre}ext_".$data['table']."` ( ";
            if($autoKey){
                $sql .= '
                    `data_id` int(10) NOT NULL AUTO_INCREMENT ,
                    PRIMARY KEY (`data_id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
                ';
            }else{
                $sql .= '
                    `data_id` int(10) DEFAULT NULL 
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
                ';
            }
            $statusSql = $this->execute($sql);
            if($statusSql === false){
                return false;
            }
            //写入数据
            return $this->data($data)->add();
        }
        if($type == 'edit'){
            if(empty($data['fieldset_id'])){
                return false;
            }
            //获取信息
            $info = $this->getInfo($data['fieldset_id']);
            //修改数据表
            $sql="
            ALTER TABLE {pre}ext_".$info['table']." RENAME TO {pre}ext_".$data['table']."
            ";
            $statusSql = $this->execute($sql);
            if($statusSql === false){
                return false;
            }
            //修改数据
            $where = array();
            $where['fieldset_id'] = $data['fieldset_id'];
            $status = $this->where($where)->data($data)->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除信息
     * @param int $fieldsetId ID
     * @return bool 删除状态
     */
    public function delData($fieldsetId)
    {
        $map = array();
        $map['fieldset_id'] = $fieldsetId;
        //获取信息
        $info = $this->getWhereInfo($map);
        if(!$info){
            $this->error = '数据不存在！';
            return false;
        }
        //删除数据表
        $sql="
            DROP TABLE `{pre}ext_".$info['table']."`
        ";
        $statusSql = $this->execute($sql);
        if($statusSql === false){
            return false;
        }
        //删除数据
        return $this->where($map)->delete();
    }

    /**
     * 通过栏目ID获取扩展字段集
     * @param int $classId 栏目ID
     * @return bool 删除状态
     */
    public function getInfoClassId($classId)
    {
        //获取栏目信息
        $classInfo=target('duxcms/Category')->getInfo($classId);
        if(empty($classInfo)){
            return false;
        }
        //获取完整栏目信息
        $classInfo=target(strtolower($classInfo['app']).'/Category'.ucfirst($classInfo['app']))->getInfo($classId);
        if(empty($classInfo)||!$classInfo['fieldset_id']){
            return false;
        }
        return $this->getInfo($classInfo['fieldset_id']);
    }

}
