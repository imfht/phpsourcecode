<?php

use yii\db\Migration;

class m200731_161943_diandi_user_bloc extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_user_bloc}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'user_id' => "int(11) NULL DEFAULT '0' COMMENT '管理员id'",
            'bloc_id' => "int(11) NULL DEFAULT '0' COMMENT '集团id'",
            'store_id' => "int(11) NULL DEFAULT '0' COMMENT '子公司id'",
            'status' => "int(11) NULL COMMENT '是否启用'",
            'create_time' => "varchar(30) NULL",
            'update_time' => "varchar(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_user_bloc}}',['id'=>'1','user_id'=>'10','bloc_id'=>'1','store_id'=>'38','status'=>'1','create_time'=>'1594112911','update_time'=>NULL]);
        $this->insert('{{%diandi_user_bloc}}',['id'=>'2','user_id'=>'20','bloc_id'=>'4','store_id'=>'43','status'=>'1','create_time'=>'1594556651','update_time'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_user_bloc}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

