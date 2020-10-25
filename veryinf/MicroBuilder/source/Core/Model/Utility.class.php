<?php
namespace Core\Model;
use Core\Util\File;
use Think\Model;

class Utility extends Model {
    protected $autoCheckFields = false;

    public static function encodePassword($input, $salt) {
        $str = "{$input}{$salt}" . C('COMMON.AUTHKEY');
        return sha1($str);
    }

    /**
     * 加载数据库配置项, 一般预留给模块封装调用, 控制器中不应该直接调用
     * @param $moduleName string 模块名称
     * @param $keys array 配置项名称
     * @return array
     */
    public static function loadSettings($moduleName, $keys) {
        $moduleName = strtoupper($moduleName);
        $m = new Model();
        $condition = '`key` IN (';
        foreach($keys as &$key) {
            $key = strtoupper($key);
            $condition .= "'{$moduleName}:{$key}',";
        }
        unset($key);
        $condition = rtrim($condition, ',');
        $condition .= ')';
        $settings = $m->table('__CORE_SETTINGS__')->where($condition)->select();
        $settings = coll_key($settings, 'key');
        $s = array();
        foreach($keys as $key) {
            $origKey = "{$moduleName}:{$key}";
            $s[$key] = unserialize($settings[$origKey]['value']);
        }
        return $s;
    }

    /**
     * 将配置项写入数据库, 一般预留给模块封装调用, 控制其中不应该直接调用
     * @param $moduleName string 模块名称
     * @param $settings array 配置项名称
     * @return bool
     */
    public static function saveSettings($moduleName, $settings) {
        $moduleName = strtoupper($moduleName);
        $ds = array();
        foreach($settings as $key => $value) {
            if(!empty($key)) {
                $key = strtoupper($key);
                $origKey = "{$moduleName}:{$key}";
                $ds[$origKey] = serialize($value);
            }
        }
        if(empty($ds)) {
            return false;
        }
        $m = new Model();
        foreach($ds as $key => $value) {
            $rec = array();
            $rec['key'] = $key;
            $rec['value'] = $value;
            $m->table('__CORE_SETTINGS__')->add($rec, array(), true);
        }
        return true;
    }

    /**
     * 上传文件保存，缩略图暂未实现
     *
     * @param string $file  上传的$_FILE字段
     * @param string $type  上传类型（将按分类保存不同子目录，image -> images）
     * @param string $sname 保存的文件名，如果为 auto 则自动生成文件名，否则请指定从附件目录开始的完整相对路径（包括文件名，不包括文件扩展名）
     * @param array $extra
     * @return array 返回结果数组，字段包括：success => bool 是否上传成功，path => 保存路径（从附件目录开始的完整相对路径）
     */
    public static function upload($file, $type = 'image', $sname = 'auto') {
        if(empty($file)) {
            return error(-1, '没有上传内容');
        }
        $type = in_array($type, array('image','audio')) ? $type : 'image';
        $settings = array(
            'image' => array(
                'storage'       => 'images/',
                'extentions'    => array('jpg', 'png'),
                'limit'         => 1024,
            )
        );

        if(!array_key_exists($type, $settings)) {
            return error(-1, '未知的上传类型');
        }
        $extention = pathinfo($file['name'], PATHINFO_EXTENSION);
        if(!in_array(strtolower($extention), $settings[$type]['extentions'])) {
            return error(-1, '不允许上传此类文件');
        }
        if(!empty($settings[$type]['limit']) && $settings[$type]['limit'] * 1024 < filesize($file['tmp_name'])) {
            return error(-1, "上传的文件超过大小限制，请上传小于 {$settings[$type]['limit']}k 的文件");
        }

        $path = MB_ROOT .'/attachment/';
        $ret = array();
        if($sname == 'auto') {
            $ret['filename'] = $settings[$type]['storage'] . date('Y/m/');
            File::mkdirs($path . $ret['filename']);
            do {
                $filename = util_random(30) . ".{$extention}";
            } while(file_exists($path .$ret['filename']. $filename));
            $ret['filename'] .= $filename;
        } else {
            $ret['filename'] = $settings[$type]['storage'] . $sname;
            mkdirs(dirname($path . $ret['filename']));
        }
        $ret['abs'] = $path . $ret['filename'];
        if(!File::move($file['tmp_name'], $ret['abs'])) {
            return error(-1, '保存上传文件失败');
        }
        $ret['url'] = attach($ret['filename']);
        return $ret;
    }

    public static function sslGenKey() {
        $config = array(
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 1024,
            'config' => MB_ROOT . 'source/Conf/openssl.cnf'
        );
        $res = openssl_pkey_new($config);
        if(empty($res)) {
            return error(-1, openssl_error_string());
        }
        $public = openssl_pkey_get_details($res);
        if(empty($public)) {
            return error(-2, openssl_error_string());
        }
        $r = openssl_pkey_export($res, $private, null, $config);
        if(empty($r)) {
            return error(-3, openssl_error_string());
        }
        openssl_free_key($res);
        $ret = array();
        $ret['public'] = $public['key'];
        $ret['private'] = $private;
        return $ret;
    }

    public static function sslTrimKey($key) {
        $pub = str_replace('-----BEGIN PUBLIC KEY-----', '', $key);
        $pub = str_replace('-----END PUBLIC KEY-----', '', $pub);
        $pub = trim($pub);
        $pub = str_replace("\r", '', $pub);
        $pub = str_replace("\n", '', $pub);
        return $pub;
    }

    public function dbRunQuery($sql) {
        if(!isset($sql) || empty($sql)) return;
        $stuff = 'mb_';
        $prefix = C('DB_PREFIX');

        $sql = str_replace("\r", "\n", str_replace(' ' . $stuff, ' ' . $prefix, $sql));
        $sql = str_replace("\r", "\n", str_replace(' `' . $stuff, ' `' . $prefix, $sql));
        $ret = array();
        $num = 0;
        foreach(explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach($queries as $query) {
                $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
            }
            $num++;
        }
        unset($sql);
        foreach($ret as $query) {
            $query = trim($query);
            if($query) {
                $this->query($query);
            }
        }
    }
    
    /**
     * 获得某个数据表的结构
     *
     * @param DB $db			数据库操作对象
     * @param string $tablename 表名
     * @return array eg: $ret = array();
     *
     * <pre>
     * // SHOW FULL COLUMNS FROM 'tablename'
     * $ret['tablename'] = '表名'; //string
     * $ret['charset']   = '字符集'; //string
     * $ret['engine']    = '存储引擎'; //string
     * $ret['increment'] = '主键自增基数'; //int
     *
     * $ret['fields'] = array(); // 数据字段
     * $ret['fields']['field1'] = array();
     * $ret['fields']['field1']['name']      = '字段名称'; //string
     * $ret['fields']['field1']['type']      = '字段类型'; //string
     * $ret['fields']['field1']['length']    = '字段长度'; //string
     * $ret['fields']['field1']['null']      = '是否可空'; //bool
     * $ret['fields']['field1']['default']   = '字段默认值'; //string
     * $ret['fields']['field1']['signed']    = '有符号, 无符号'; //bool
     * $ret['fields']['field1']['increment'] = '是否增字段'; //bool
     * $ret['fields']['field1']['comment']   = '备注信息'; //string
     * $ret['fields']['field2'] = ...
     *
     * // SHOW INDEX FROM 'tablename'
     * $ret['indexes'] = array(); // 数据索引项
     * $ret['indexes']['index1'] = array(); // 数据索引项
     * $ret['indexes']['index1']['name']   = '索引名称'; // string
     * $ret['indexes']['index1']['type']   = '索引类型'; // primary|index|unique
     * $ret['indexes']['index1']['fields'] = array('f1','f2',...) // '索引包含的字段'; array 每个元素为字段名
     * $ret['indexes']['index2'] = ...;
     * ...
     * </pre>
     */
    public function dbTableSchema($tablename = '', $prefix = null) {
        if(!isset($prefix)) {
            $prefix = C('DB_PREFIX');
        }
        $result = $this->query("SHOW TABLE STATUS LIKE '{$prefix}{$tablename}'");
        if(empty($result)) {
            return array();
        }
        $result = $result[0];
        $ret['tablename'] = $result['Name'];
        $ret['charset'] = $result['Collation'];
        $ret['engine'] = $result['Engine'];
        $ret['increment'] = $result['Auto_increment'];
        $result = $this->query("SHOW FULL COLUMNS FROM `{$prefix}{$tablename}`");
        foreach($result as $value) {
            $temp = array();
            $type = explode(" ", $value['Type'], 2);
            $temp['name'] = $value['Field'];
            $pieces = explode('(', $type[0], 2);
            $temp['type'] = $pieces[0];
            $temp['length'] = rtrim($pieces[1], ')');
            $temp['null'] = $value['Null'] != 'NO';
            if(isset($value['Default'])) {
                $temp['default'] = $value['Default'];
            }
            $temp['signed'] = empty($type[1]);
            $temp['increment'] = $value['Extra'] == 'auto_increment';
            $ret['fields'][$value['Field']] = $temp;
        }
        $result = $this->query("SHOW INDEX FROM `{$prefix}{$tablename}`");
        foreach($result as $value) {
            $ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];
            $ret['indexes'][$value['Key_name']]['type'] = ($value['Key_name'] == 'PRIMARY') ? 'primary' : ($value['Non_unique'] == 0 ? 'unique' : 'index');
            $ret['indexes'][$value['Key_name']]['fields'][] = $value['Column_name'];
        }
        return $ret;
    }

    public function dbTableCreateSql($schema) {
        $pieces = explode('_', $schema['charset']);
        $charset = $pieces[0];
        $engine = $schema['engine'];
        $sql = "CREATE TABLE IF NOT EXISTS `{$schema['tablename']}` (\n";
        foreach ($schema['fields'] as $value) {
            $piece = $this->dbBuildFieldSql($value);
            $sql .= "`{$value['name']}` {$piece},\n";
        }
        foreach ($schema['indexes'] as $value) {
            $fields = implode('`,`', $value['fields']);
            if($value['type'] == 'index') {
                $sql .= "KEY `{$value['name']}` (`{$fields}`),\n";
            }
            if($value['type'] == 'unique') {
                $sql .= "UNIQUE KEY `{$value['name']}` (`{$fields}`),\n";
            }
            if($value['type'] == 'primary') {
                $sql .= "PRIMARY KEY (`{$fields}`),\n";
            }
        }
        $sql = rtrim($sql);
        $sql = rtrim($sql, ',');

        $sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;\n\n";
        return $sql;
    }

    /**
     * 比较两个表结构
     *
     * @param array $table1
     * @param array $table2
     * @return 返回两个数据结构差异项. eg: $ret = array();
     * <pre>
     * $ret['diffs']['tablename'] = true; //如果表名不同, 记录此元素
     * $ret['diffs']['charset'] = true; //如果字符集不同, 记录此元素
     * $ret['diffs']['engine'] = true; //如果存储引擎不同, 记录此元素
     * $ret['diffs']['increment'] = true; //如果自增基数不同, 记录此元素
     *
     * $ret['fields'] 字段差异
     * $ret['fields']['greater'] $table1中存在, $table2中不存在的字段
     * $ret['fields']['less'] $table1中不存在, $table2中存在的字段
     * $ret['fields']['diff'] $table1和$table2都存在, 但是定义不同的字段
     *
     * $ret['indexes'] 索引差异
     * $ret['indexes']['greater']  $table1中存在, $table2中不存在的索引
     * $ret['indexes']['less'] $table1中不存在, $table2中存在的索引
     * $ret['indexes']['diff'] $table1和$table2都存在, 但是定义不同的索引
     * </pre>
     */
    public function dbSchemaCompare($table1, $table2) {
        $table1['charset'] == $table2['charset'] ? '' : $ret['diffs']['charset'] = true;
        $table1['engine'] == $table2['engine'] ? '' : $ret['diffs']['engine'] = true;
        $table1['increment'] == $table2['increment'] ? '' : $ret['diffs']['increment'] = true;

        $fields1 = array_keys($table1['fields']);
        $fields2 = array_keys($table2['fields']);
        $diffs = array_diff($fields1, $fields2);
        if(!empty($diffs)) {
            $ret['fields']['greater'] = array_values($diffs);
        }
        $diffs = array_diff($fields2, $fields1);
        if(!empty($diffs)) {
            $ret['fields']['less'] = array_values($diffs);
        }
        $diffs = array();
        $intersects = array_intersect($fields1, $fields2);
        if(!empty($intersects)) {
            foreach($intersects as $field) {
                if($table1['fields'][$field] != $table2['fields'][$field]) {
                    $diffs[] = $field;
                }
            }
        }
        if(!empty($diffs)) {
            $ret['fields']['diff'] = array_values($diffs);
        }

        $indexes1 = array_keys($table1['indexes']);
        $indexes2 = array_keys($table2['indexes']);
        $diffs = array_diff($indexes1, $indexes2);
        if(!empty($diffs)) {
            $ret['indexes']['greater'] = array_values($diffs);
        }
        $diffs = array_diff($indexes2, $indexes1);
        if(!empty($diffs)) {
            $ret['indexes']['less'] = array_values($diffs);
        }
        $diffs = array();
        $intersects = array_intersect($indexes1, $indexes2);
        if(!empty($intersects)) {
            foreach($intersects as $index) {
                if($table1['indexes'][$index] != $table2['indexes'][$index]) {
                    $diffs[] = $index;
                }
            }
        }
        if(!empty($diffs)) {
            $ret['indexes']['diff'] = array_values($diffs);
        }

        return $ret;
    }
    /**
     * 创建修复两张表差异的SQL语句
     *
     * @param string $schema1 表结构 需要修复的表
     * @param string $schema2 表结构 基准表
     * @param bool $strict 使用严格模式, 严格模式将会把表2完全变成表1的结构, 否则将只处理表2种大于表1的内容(多出的字段和索引)
     * @return array $sql 修复SQL语句组成的数组
     */
    function dbTableFixSql($schema1, $schema2, $strict = false) {
        if(empty($schema1)) {
            return array($this->dbTableCreateSql($schema2));
        }
        $diff = $result = $this->dbSchemaCompare($schema1, $schema2);
        if(!empty($diff['diffs']['tablename'])) {
            return array($this->dbTableCreateSql($schema2));
        }
        $sqls = array();
        if(!empty($diff['diffs']['engine'])) {
            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` ENGINE = {$schema2['engine']}";
        }

        if(!empty($diff['diffs']['charset'])) {
            $pieces = explode('_', $schema2['charset']);
            $charset = $pieces[0];
            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DEFAULT CHARSET = {$charset}";
        }

        if(!empty($diff['fields'])) {
            if(!empty($diff['fields']['less'])) {
                foreach($diff['fields']['less'] as $fieldname) {
                    $field = $schema2['fields'][$fieldname];
                    $piece = $this->dbBuildFieldSql($field);
                    if(!empty($field['rename']) && !empty($schema1['fields'][$field['rename']])) {
                        $sql = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['rename']}` `{$field['name']}` {$piece}";
                        unset($schema1['fields'][$field['rename']]);
                    } else {
                        if($field['position']) {
                            $pos = ' ' . $field['position'];
                        }
                        $sql = "ALTER TABLE `{$schema1['tablename']}` ADD `{$field['name']}` {$piece}{$pos}";
                    }
                    //如果此条SQL语句为自增，则需要先把其它自增字段去掉，并把此字段设置为主键
                    $primary = array();
                    $isincrement = array();
                    if (strexists($sql, 'AUTO_INCREMENT')) {
                        $isincrement = $field;
                        $sql =  str_replace('AUTO_INCREMENT', '', $sql);
                        foreach ($schema1['fields'] as $field) {
                            if ($field['increment'] == 1) {
                                $primary = $field;
                                break;
                            }
                        }
                        if (!empty($primary)) {
                            $piece = $this->dbBuildFieldSql($primary);
                            if (!empty($piece)) {
                                $piece = str_replace('AUTO_INCREMENT', '', $piece);
                            }
                            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$primary['name']}` `{$primary['name']}` {$piece}";
                        }
                    }
                    $sqls[] = $sql;
                }
            }
            if(!empty($diff['fields']['diff'])) {
                foreach($diff['fields']['diff'] as $fieldname) {
                    $field = $schema2['fields'][$fieldname];
                    $piece = $this->dbBuildFieldSql($field);
                    if(!empty($schema1['fields'][$fieldname])) {
                        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['name']}` `{$field['name']}` {$piece}";
                    }
                }
            }
            if($strict && !empty($diff['fields']['greater'])) {
                foreach($diff['fields']['greater'] as $fieldname) {
                    if(!empty($schema1['fields'][$fieldname])) {
                        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$fieldname}`";
                    }
                }
            }
        }

        if(!empty($diff['indexes'])) {
            if(!empty($diff['indexes']['less'])) {
                foreach($diff['indexes']['less'] as $indexname) {
                    $index = $schema2['indexes'][$indexname];
                    $piece = $this->dbBuildIndexSql($index);
                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` ADD {$piece}";
                }
            }
            if(!empty($diff['indexes']['diff'])) {
                foreach($diff['indexes']['diff'] as $indexname) {
                    $index = $schema2['indexes'][$indexname];
                    $piece = $this->dbBuildIndexSql($index);

                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP ".($indexname == 'PRIMARY' ? " PRIMARY KEY " : "{$indexname}").", ADD {$piece}";
                }
            }
            if($strict && !empty($diff['indexes']['greater'])) {
                foreach($diff['indexes']['greater'] as $indexname) {
                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$indexname}`";
                }
            }
        }
        if (!empty($isincrement)) {
            $piece = $this->dbBuildFieldSql($isincrement);
            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$isincrement['name']}` `{$isincrement['name']}` {$piece}";
        }
        return $sqls;
    }

    private function dbBuildIndexSql($index) {
        $piece = '';
        $fields = implode('`,`', $index['fields']);
        if($index['type'] == 'index') {
            $piece .= " INDEX `{$index['name']}` (`{$fields}`)";
        }
        if($index['type'] == 'unique') {
            $piece .= "UNIQUE `{$index['name']}` (`{$fields}`)";
        }
        if($index['type'] == 'primary') {
            $piece .= "PRIMARY KEY (`{$fields}`)";
        }
        return $piece;
    }

    private function dbBuildFieldSql($field) {
        if(!empty($field['length'])) {
            $length = "({$field['length']})";
        } else {
            $length = '';
        }

        $signed  = empty($field['signed']) ? ' unsigned' : '';
        if(empty($field['null'])) {
            $null = ' NOT NULL';
        } else {
            $null = '';
        }
        if(isset($field['default'])) {
            $default = " DEFAULT '" . $field['default'] . "'";
        } else {
            $default = '';
        }
        if($field['increment']) {
            $increment = ' AUTO_INCREMENT';
        } else {
            $increment = '';
        }
        return "{$field['type']}{$length}{$signed}{$null}{$default}{$increment}";
    }

}
