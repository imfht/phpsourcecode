<?php

use yii\db\Migration;

class m200731_161942_ai_member extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%ai_member}}', [
            'user_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '人脸招聘'",
            'face_group_id' => "int(11) NOT NULL COMMENT '人脸库组id'",
            'nickName' => "varchar(255) NOT NULL DEFAULT ''",
            'face_image' => "varchar(255) NOT NULL DEFAULT '' COMMENT '人脸照片'",
            'gender' => "varchar(10) NOT NULL DEFAULT '0'",
            'face_token' => "varchar(255) NULL COMMENT '脸部图片唯一标识'",
            'wxapp_id' => "int(11) unsigned NULL",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'update_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'ai_age' => "int(11) NULL COMMENT 'ai年龄'",
            'ai_gender' => "enum('male','female') NULL COMMENT 'ai性别'",
            'ai_glasses' => "varchar(255) NULL",
            'ai_race' => "varchar(255) NULL COMMENT 'ai种族'",
            'ai_emotion' => "varchar(255) NULL COMMENT 'ai情绪'",
            'face_shape' => "varchar(255) NULL COMMENT 'ai脸型'",
            'ai_quality_blur' => "varchar(255) NULL COMMENT 'ai图片质量1'",
            'ai_quality_illumination' => "varchar(255) NULL COMMENT 'ai图片质量1'",
            'ai_quality_completeness' => "varchar(255) NULL COMMENT 'ai图片质量1'",
            'PRIMARY KEY (`user_id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='店滴ai会员表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%ai_member}}',['user_id'=>'10','face_group_id'=>'1','nickName'=>'','face_image'=>'http://www.cc.com/upload/202001/19/cf072bdc-e936-3bc6-b7e3-a8684676750d.jpg','gender'=>'0','face_token'=>'3d809bfd3d06e83c796592cf3c55fcbe','wxapp_id'=>NULL,'create_time'=>'1579367498','update_time'=>'1579367498','ai_age'=>'30','ai_gender'=>'male','ai_glasses'=>'none','ai_race'=>'yellow','ai_emotion'=>'neutral','face_shape'=>NULL,'ai_quality_blur'=>NULL,'ai_quality_illumination'=>NULL,'ai_quality_completeness'=>NULL]);
        $this->insert('{{%ai_member}}',['user_id'=>'11','face_group_id'=>'1','nickName'=>'','face_image'=>'http://www.cc.com/upload/202001/19/de7bf97b-646f-3c41-883e-217e545abcae.jpg','gender'=>'0','face_token'=>'e2c79fad75fe77570b8c0f7b4021a89f','wxapp_id'=>NULL,'create_time'=>'1579368388','update_time'=>'1579368388','ai_age'=>'39','ai_gender'=>'male','ai_glasses'=>'none','ai_race'=>'white','ai_emotion'=>'angry','face_shape'=>NULL,'ai_quality_blur'=>NULL,'ai_quality_illumination'=>NULL,'ai_quality_completeness'=>NULL]);
        $this->insert('{{%ai_member}}',['user_id'=>'12','face_group_id'=>'1','nickName'=>'','face_image'=>'http://www.cc.com/upload/202001/19/de7bf97b-646f-3c41-883e-217e545abcae.jpg','gender'=>'0','face_token'=>'e2c79fad75fe77570b8c0f7b4021a89f','wxapp_id'=>NULL,'create_time'=>'1579368861','update_time'=>'1579368861','ai_age'=>'39','ai_gender'=>'male','ai_glasses'=>'none','ai_race'=>'white','ai_emotion'=>'angry','face_shape'=>NULL,'ai_quality_blur'=>NULL,'ai_quality_illumination'=>NULL,'ai_quality_completeness'=>NULL]);
        $this->insert('{{%ai_member}}',['user_id'=>'13','face_group_id'=>'1','nickName'=>'','face_image'=>'http://www.cc.com/upload/202001/19/de7bf97b-646f-3c41-883e-217e545abcae.jpg','gender'=>'0','face_token'=>'e2c79fad75fe77570b8c0f7b4021a89f','wxapp_id'=>NULL,'create_time'=>'1579369110','update_time'=>'1579369110','ai_age'=>'39','ai_gender'=>'male','ai_glasses'=>'none','ai_race'=>'white','ai_emotion'=>'angry','face_shape'=>NULL,'ai_quality_blur'=>NULL,'ai_quality_illumination'=>NULL,'ai_quality_completeness'=>NULL]);
        $this->insert('{{%ai_member}}',['user_id'=>'14','face_group_id'=>'1','nickName'=>'','face_image'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=485966869,2300262866&fm=26&gp=0.jpg','gender'=>'0','face_token'=>'10ec6989393375e86227ba52aca8da15','wxapp_id'=>NULL,'create_time'=>'1579503349','update_time'=>'1579503349','ai_age'=>'40','ai_gender'=>'male','ai_glasses'=>'none','ai_race'=>'yellow','ai_emotion'=>'neutral','face_shape'=>NULL,'ai_quality_blur'=>NULL,'ai_quality_illumination'=>NULL,'ai_quality_completeness'=>NULL]);
        $this->insert('{{%ai_member}}',['user_id'=>'15','face_group_id'=>'1','nickName'=>'','face_image'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=485966869,2300262866&fm=26&gp=0.jpg','gender'=>'0','face_token'=>'0bec06b07a7a99bfbba58557b271e8dd','wxapp_id'=>NULL,'create_time'=>'1579503350','update_time'=>'1579503350','ai_age'=>'18','ai_gender'=>'female','ai_glasses'=>'none','ai_race'=>'white','ai_emotion'=>'happy','face_shape'=>NULL,'ai_quality_blur'=>NULL,'ai_quality_illumination'=>NULL,'ai_quality_completeness'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%ai_member}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

