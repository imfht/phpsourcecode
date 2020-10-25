<?php

namespace Db\Model;

use Think\Model;

/**
 * 创建实体数据库表
 */
class TableModel extends Model {

    /**
     * 创建新表
     * @param  string $tname 表名
     * @param  string $tnamec 别称
     * @return integer        注册结果
     */
    public function CreateTable($tname, $cell) {

        $tablecell = "";
        foreach ($cell as $key => $value) {

            $tablecell.=$this->getcell($value);
        }

        $sql = 'show tables like \'' . $tname . '\'';
        $result = M()->execute($sql);

        if (!$result) {
            if ($tablecell) {
                $CREATESQL = '
					CREATE TABLE `' . $tname . '` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  ' . $tablecell . '
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            }
            M()->execute($CREATESQL);

            $res = M()->execute($sql);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除表
     * @param  string $tname 表名
     * @return integer       注册结果
     */
    public function DelTable($tname){
        $sql = "DROP TABLE $tname;";
        return M()->execute($sql);
    }

    /**
     * 获取单挑数据
     * @param  string $tableName 表名
     * @param  boole $id 编号
     */
    public function GetData($tableName, $id) {

        $Model = new Model();
        $tmd = $Model->table($tableName);

        return $tmd->where('id=' . $id)->find();
    }

    /**
     * 通过视图名称，获取数据
     * @param  string $tableName 表名
     * @param  boole $showColumn 是否显示中文字段名
     */
    public function GetDataForTable($tableName, $page_size, $column, $where) {

        $result = array();

        $Model = new Model();
        $tmd = $Model->table($tableName);

        if ($where) {
            $count = $tmd->where($where)->count(); // 查询满足要求的总记录数
            $Page = new \Think\Page($count, $page_size); // 实例化分页类 传入总记录数和每页显示的记录数
            $result['page'] = $Page->show(); // 分页显示输出

            $tmd = $Model->table($tableName);
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $result['list'] = $tmd->field($column)->where($where)->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();

            return $result;
        } else {

            $count = $tmd->count(); // 查询满足要求的总记录数
            $Page = new \Think\Page($count, $page_size); // 实例化分页类 传入总记录数和每页显示的记录数

            $result['page'] = $Page->show(); // 分页显示输出

            $tmd = $Model->table($tableName);
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $result['list'] = $tmd->field($column)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

            return $result;
        }
    }

    /**
     * 插入数据
     * @param  string $datalist 数据集合
     */
    public function Insert_Form_Data($datalist) {

        $db_form = new DbformModel();

        $dbcell = new DbcellModel();

        if ($datalist) {
            $tables = $db_form->GetDBFormVid($datalist[0]['vid']);

            //获取s_dbcell内容
            $sdbcolumn_list = $dbcell->GetListForTid($tables['tid']);
            $sql = "INSERT INTO " . $tables['form_table'] . "(column) VALUES (key)";
            foreach ($datalist as $key => $value) {
                $column.=',' . $value['name'];

                //判断是否是密码
                $type = GetPWDByName($value['name'], $sdbcolumn_list);

                if ($type['flx'] == 'password') {
                    $dtvalue.=',\'' . md5($value['value']) . '\'';
                } else {
                    $dtvalue.=',\'' . $value['value'] . '\'';
                }
            }
            $sql = str_replace('column', substr($column, 1), $sql);
            $sql = str_replace('key', substr($dtvalue, 1), $sql);

            return $this->execute($sql);
        }
    }

    /**
     * 更新数据
     * @param  string $datalist 数据集合
     */
    public function Update_Form_Data($datalist, $id) {

        if ($datalist) {
            $db_form = new DbformModel();
            $tables = $db_form->GetDBFormVid($datalist[0]['vid']);

            $Model = new Model();
            $tmd = $Model->table($tables['form_table']);

            // 要修改的数据对象属性赋值
            foreach ($datalist as $key => $value) {
                $data[$value['name']] = $value['value'];
            }

            return $tmd->where('id=' . $id)->save($data); // 根据条件更新记录
        }
    }

    /**
     * 删除数据
     * @param  string $datalist 数据集合
     */
    public function Del_Form_Data($vid, $mid) {

        $db_form = new DbformModel();
        $tables = $db_form->GetDBFormVid($vid);

        $Model = new Model();
        $tmd = $Model->table($tables['form_table']);

        $result = $tmd->where('id=' . $mid)->delete();

        return $result;
    }

    //不同字段类型，返回不同语句
    private function getcell($value) {
        switch ($value['ttype']) {
            case 'file':
                return $tablecell . '`' . $value['tname'] . '` varchar(500) CHARACTER SET utf8 DEFAULT NULL,';
                break;
            case 'password':
            case 'image':
                return $tablecell . '`' . $value['tname'] . '` varchar(500) CHARACTER SET utf8 DEFAULT NULL,';
                break;
            case 'richtext':
                return $tablecell . '`' . $value['tname'] . '` text CHARACTER SET utf8 DEFAULT NULL,';
                break;
            case 'decimal':
                return $tablecell . '`' . $value['tname'] . '` decimal(0,4) CHARACTER SET utf8 DEFAULT NULL,';
                break;
            case 'varchar':
                return $tablecell . '`' . $value['tname'] . '` ' . $value['ttype'] . '(' . $value['tcd'] . ') CHARACTER SET utf8 DEFAULT NULL,';
                break;
            case 'text':
                return $tablecell . '`' . $value['tname'] . '` ' . $value['ttype'] . ' CHARACTER SET utf8,';
                break;
            case 'datetime':
                return $tablecell . '`' . $value['tname'] . '` ' . $value['ttype'] . ' DEFAULT NULL,';
                break;
            case 'datetime':
                return $tablecell . '`' . $value['tname'] . '` ' . $value['ttype'] . '(' . $value['tcd'] . ',0) NOT NULL,';
                break;
            case 'int':
                return $tablecell . '`' . $value['tname'] . '` ' . $value['ttype'] . '(' . $value['tcd'] . ') NOT NULL,';
                break;
            default:
                # code...
                break;
        }
    }

}
