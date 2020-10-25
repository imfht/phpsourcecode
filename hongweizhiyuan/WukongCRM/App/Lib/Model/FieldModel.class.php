<?php 
    /*自定义字段类
    *许浩光
    *add 添加字段
    *delete 删除字段
    *edit 修改字段
    */
    class FieldModel extends Model{
        //设置默认表名为空
        protected $tableName = ''; 
        protected $trueTableName = ''; 
        protected $queryStr = ''; 
        protected $_validate;
        public function _initialize(){
            $validate[0] = 'field';
            $validate[1] = '/^[a-z]([a-z]|_)+[a-z]$/i';
            $validate[2] = L('FIELD NAME FORMAT IS INCORRECT');
            $validate[3] = 1;
            $validate[4] = 'regex';
            $validate[5] = 3;
            $this->_validate[] = $validate;
        }
        //修改字段
        public function add($data = false){
            if(!$this->autoValidation($data)) return false;
            $this->tableName = $data['is_main']?$data['model']:$data['model'].'_data';
            $maxlength = (intval($data['max_length']) != 0)? intval($data['max_length']): 255;
            switch($data['form_type']) {
				case 'address':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
                    return $this->execute($this->queryStr);
                break;
                
                case 'box':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` VARCHAR($maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]'";
                    return $this->execute($this->queryStr);
                break;
				
                case 'textarea':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
                    return $this->execute($this->queryStr);
                break;
                
                case 'editor':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
                    return $this->execute($this->queryStr);
                break;
                
                case 'number':
                    $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
                    $maxlength = intval($maxlength) > 11 ? 11:intval($maxlength);
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` int ($maxlength) NOT NULL DEFAULT '$defaultvalue'";
                    return $this->db->execute($this->queryStr);
                break;
				
				case 'floatnumber':
                    $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
                    $maxlength = intval($maxlength) > 11 ? 9:(intval($maxlength)-2);
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` float ($maxlength,2) NOT NULL DEFAULT '$defaultvalue'";
                    return $this->db->execute($this->queryStr);
                break;
                
                case 'datetime':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` int (10) NOT NULL ";
                    return $this->db->execute($this->queryStr);
                break;
                default:
                    $maxlength = $maxlength < 20774 ? $maxlength : 333;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` VARCHAR($maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]'";
                    return $this->execute($this->queryStr);
                break;
            }
        }
        
        public function save($data = false){
            if(!$this->autoValidation($data)) return false;
            $this->tableName = $data['is_main']?$data['model']:$data['model'].'_data';
            $maxlength = ($data['max_length'] && intval($data['max_length']) != 0)? intval($data['max_length']): 255;
            switch($data['form_type']) {
				case 'address':
					$this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
                    return $this->execute($this->queryStr);
                break;
				
                case 'box':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` VARCHAR( $maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]'";
                    return $this->execute($this->queryStr);
                break;
                
                case 'textarea':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
                    return $this->execute($this->queryStr);
                break;
                
                case 'editor':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
                    return $this->execute($this->queryStr);
                break;
                
                case 'number':
                    $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
                    $maxlength = intval($maxlength) > 11 ? 9:(intval($maxlength)-2);
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` int ($maxlength) NOT NULL DEFAULT '$defaultvalue'";
                    return $this->db->execute($this->queryStr);
                break;
				
				case 'floatnumber':
                    $defaultvalue = abs(intval($data['default_value'])) > 32767.99 ? 32767.99 : intval($data['default_value']);
                    $maxlength = $maxlength > 11 ? 11:$maxlength;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` int ($maxlength) NOT NULL DEFAULT '$defaultvalue'";
                    return $this->db->execute($this->queryStr);
                break;
                
                case 'datetime':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` int (10) NOT NULL ";
                    return $this->db->execute($this->queryStr);
                break;
				
				default:
                    $maxlength = $maxlength < 20774 ? $maxlength : 333;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` VARCHAR( $maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]'";
                    return $this->execute($this->queryStr);
                break;
            }
        }
        public function getLastSql() {
            return $this->queryStr;
        }
        // 鉴于getLastSql比较常用 增加_sql 别名
        public function _sql(){
            return $this->getLastSql();
        }
        
        public function delete($data){
            $this->tableName = $data['is_main']?$data['model']:$data['model'].'_data';
            $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` DROP `$data[field]`;";
            return $this->execute($this->queryStr);
        }
    }