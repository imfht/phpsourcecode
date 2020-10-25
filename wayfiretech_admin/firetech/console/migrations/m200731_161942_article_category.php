<?php

use yii\db\Migration;

class m200731_161942_article_category extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%article_category}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'title' => "varchar(30) NOT NULL",
            'displayorder' => "tinyint(3) unsigned NOT NULL",
            'pcate' => "int(11) NULL DEFAULT '0'",
            'type' => "varchar(15) NOT NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文章分类'");
        
        /* 索引设置 */
        $this->createIndex('type','{{%article_category}}','type',0);
        
        
        /* 表数据 */
        $this->insert('{{%article_category}}',['id'=>'14','title'=>'关于我们','displayorder'=>'1','pcate'=>'10','type'=>'about']);
        $this->insert('{{%article_category}}',['id'=>'8','title'=>'智能设备','displayorder'=>'1','pcate'=>'10','type'=>'facility']);
        $this->insert('{{%article_category}}',['id'=>'9','title'=>'应用场景','displayorder'=>'1','pcate'=>'10','type'=>'scene']);
        $this->insert('{{%article_category}}',['id'=>'10','title'=>'网站内容','displayorder'=>'1','pcate'=>'0','type'=>'website']);
        $this->insert('{{%article_category}}',['id'=>'11','title'=>'特色优势','displayorder'=>'1','pcate'=>'10','type'=>'superiority']);
        $this->insert('{{%article_category}}',['id'=>'12','title'=>'开源内容','displayorder'=>'1','pcate'=>'10','type'=>'open']);
        $this->insert('{{%article_category}}',['id'=>'13','title'=>'方案介绍','displayorder'=>'1','pcate'=>'10','type'=>'website1']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%article_category}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

