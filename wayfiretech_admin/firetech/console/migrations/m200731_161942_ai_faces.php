<?php

use yii\db\Migration;

class m200731_161942_ai_faces extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%ai_faces}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'ai_user_id' => "int(11) NULL",
            'ai_group_id' => "int(11) NULL",
            'ai_face_status' => "int(11) NULL",
            'face_image' => "varchar(255) NULL",
            'face_token' => "varchar(255) NULL",
            'createtime' => "varchar(30) NULL",
            'updatetime' => "varchar(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='ai检测用户'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%ai_faces}}',['id'=>'23','ai_user_id'=>'10','ai_group_id'=>'1','ai_face_status'=>'0','face_image'=>'upload/202001/19/e8a7b515-6343-3e87-b206-44653934970c.jpg','face_token'=>'2b0e0b643f1550090e0df5723b500b18','createtime'=>'','updatetime'=>'']);
        $this->insert('{{%ai_faces}}',['id'=>'25','ai_user_id'=>'13','ai_group_id'=>'1','ai_face_status'=>'0','face_image'=>'http://www.cc.com/upload/202001/19/de7bf97b-646f-3c41-883e-217e545abcae.jpg','face_token'=>'e2c79fad75fe77570b8c0f7b4021a89f','createtime'=>'1579369175','updatetime'=>'']);
        $this->insert('{{%ai_faces}}',['id'=>'26','ai_user_id'=>'14','ai_group_id'=>'1','ai_face_status'=>NULL,'face_image'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=485966869,2300262866&fm=26&gp=0.jpg','face_token'=>'10ec6989393375e86227ba52aca8da15','createtime'=>'1579503350','updatetime'=>'1579503350']);
        $this->insert('{{%ai_faces}}',['id'=>'27','ai_user_id'=>'15','ai_group_id'=>'1','ai_face_status'=>NULL,'face_image'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=485966869,2300262866&fm=26&gp=0.jpg','face_token'=>'0bec06b07a7a99bfbba58557b271e8dd','createtime'=>'1579503351','updatetime'=>'1579503351']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%ai_faces}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

