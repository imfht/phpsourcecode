<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Init_ciplus extends CI_Migration {
    public function up() {
        $this->create_api();
        $this->create_module();
        $this->create_role();
        $this->create_role_api();
        $this->create_role_user();
        $this->create_user();
        $this->create_user_info();
        $this->init_data();
    }

    private function create_api() {
        $this->dbforge->add_field(array(
            // 序列ID
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            // 唯一标识
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'unique' => TRUE
            ),
            // 接口标名
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            // 模块归属
            'module' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            // 接口路径
            'path' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            // 必填参数
            'required' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '[]',
            ),
            // 选填参数
            'optional' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '[]',
            ),
            // 接口方法
            'method' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => 'request',
            ),
            // 是否验证
            'validated' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 1,
            ),
            // 是否可用
            'usable' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 1,
            ),
            // 只读
            'readonly' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 0,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'api',
            TRUE,
            $this->attribute('接口表：#####')
        );
    }

    private function create_module() {
        $this->dbforge->add_field(array(
            // 序列ID
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            // 唯一标识
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'unique' => TRUE
            ),
            // 模块标名
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            // 父模块ID
            'parent_id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'default' => 0,
            ),
            // 只读
            'readonly' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 0,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'module',
            TRUE,
            $this->attribute('模块表：#####')
        );
    }

    private function create_role() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => true
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => '140',
                'null' => TRUE,
            ),
            'readonly' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 0,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'role',
            TRUE,
            $this->attribute('角色表：#####')
        );
    }

    private function create_role_api() {
        $this->dbforge->add_field(array(
            'role_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
            ),
            'api_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
            ),
        ));
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'role_api',
            TRUE,
            $this->attribute('角色表：接口权限')
        );
    }

    private function create_role_user() {
        $this->dbforge->add_field(array(
            'role_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
        ));
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'role_user',
            TRUE,
            $this->attribute('角色表：用户角色')
        );
    }

    private function create_user() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'account' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'unique' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
                'unique' => TRUE
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'unique' => TRUE
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'create_time' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'default' => 1546315200
            ),
            'usable' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => FALSE,
                'default' => 1,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'user',
            TRUE,
            $this->attribute('用户表：#####')
        );
    }

    private function create_user_info() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'sex' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => FALSE,
                'default' => 0,
            ),
            'avatar' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => TRUE,
                'default' => '/'
            ),
            'area' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'city' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'province' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'country' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'introduction' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'user_info',
            TRUE,
            $this->attribute('用户表：基本信息')
        );
    }

    private function attribute($comment) {
        return array(
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8',
            'COMMENT' => "'" . $comment . "'"
        );
    }

    private function init_data() {
        $id = $this->db->insert(CIPLUS_DB_PREFIX . 'user',
            array(
                'account' => 'admin',
                'email' => 'admin@cprap.com',
                'password' => 'b34e50b8b8b4b1fe831a20e37db6285b38adb5c0510afdd7dd62ac5a8412e5be4685b5a8408cf3cfc7b70058be2309a759009be6273ccc0a44f3fa57391abc80qfPIU29n4I6A0e8kEPx/RvuHex93c9Bl9sN9X333S7kspgczaGFXuC00KBoBvctArV/3yC0h8oErepMIFMFKBg==',
                'create_time' => time()
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'user_info',
            array(
                'id' => $id,
                'name' => 'administrator',
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'role',
            array(
                'key' => 'admin',
                'name' => '超级管理员',
                'description' => '系统超级管理员',
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'role',
            array(
                'key' => 'manager',
                'name' => '管理员',
                'description' => '系统管理员',
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'role_user',
            array(
                'role_key' => 'admin',
                'user_id' => $id,
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'login',
                'title' => '用户登录',
                'module' => 'system',
                'path' => 'passport/login',
                'required' => json_encode(array("password")),
                'optional' => json_encode(array("passport", "account", "email", "phone", "header")),
                'method' => 'request',
                'validated' => 0,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'info',
                'title' => '登录凭证',
                'module' => 'system',
                'path' => 'passport/info',
                'required' => json_encode(array("token")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 0,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'reg',
                'title' => '用户注册',
                'module' => 'system',
                'path' => 'passport/reg',
                'required' => json_encode(array("password")),
                'optional' => json_encode(array("account", "email", "phone", "repassword")),
                'method' => 'request',
                'validated' => 0,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'refresh',
                'title' => '更新会话',
                'module' => 'system',
                'path' => 'passport/refresh',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'logout',
                'title' => '注销登录',
                'module' => 'system',
                'path' => 'passport/logout',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_all',
                'title' => '全部接口',
                'module' => 'setting',
                'path' => 'setting/api_all',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_more',
                'title' => '更多接口',
                'module' => 'setting',
                'path' => 'setting/api_more',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n", "title")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_add',
                'title' => '添加接口',
                'module' => 'setting',
                'path' => 'setting/api_add',
                'required' => json_encode(array("title", "path")),
                'optional' => json_encode(array("required", "optional", "method", "validated")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_edit',
                'title' => '修改接口',
                'module' => 'setting',
                'path' => 'setting/api_edit',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array("title", "path", "required", "optional", "method", "validated")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_del',
                'title' => '移除接口',
                'module' => 'setting',
                'path' => 'setting/api_del',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_revive',
                'title' => '恢复接口',
                'module' => 'setting',
                'path' => 'setting/api_revive',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_all',
                'title' => '全部模块',
                'module' => 'setting',
                'path' => 'setting/module_all',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_more',
                'title' => '更多模块',
                'module' => 'setting',
                'path' => 'setting/module_more',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_add',
                'title' => '添加模块',
                'module' => 'setting',
                'path' => 'setting/module_add',
                'required' => json_encode(array("key", "name")),
                'optional' => json_encode(array("parent_key")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_edit',
                'title' => '修改模块',
                'module' => 'setting',
                'path' => 'setting/module_edit',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array("name", "parent_key")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_del',
                'title' => '移除模块',
                'module' => 'setting',
                'path' => 'setting/module_del',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_all',
                'title' => '全部角色',
                'module' => 'role',
                'path' => 'role/all',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_more',
                'title' => '更多角色',
                'module' => 'role',
                'path' => 'role/more',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_add',
                'title' => '添加角色',
                'module' => 'role',
                'path' => 'role/add',
                'required' => json_encode(array("key", "name")),
                'optional' => json_encode(array("description")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_edit',
                'title' => '编辑角色',
                'module' => 'role',
                'path' => 'role/edit',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array("name", "description")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_del',
                'title' => '删除角色',
                'module' => 'role',
                'path' => 'role/del',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_user',
                'title' => '用户列表',
                'module' => 'role',
                'path' => 'role/users',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n", "key", "value")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_api',
                'title' => '接口权限列表',
                'module' => 'role',
                'path' => 'role/apis',
                'required' => json_encode(array("key")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_api_edit',
                'title' => '接口权限编辑',
                'module' => 'role',
                'path' => 'role/api_edit',
                'required' => json_encode(array("dict", 'role')),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'system',
                'name' => '系统模块',
                'readonly' => 1,
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'setting',
                'name' => '设置模块',
                'readonly' => 1,
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'role',
                'name' => '角色权限',
                'readonly' => 1,
            ));
    }

    public function down() {
//        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'api');
//        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role');
//        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role_api');
//        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role_user');
//        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'user');
//        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'user_info');
    }
}