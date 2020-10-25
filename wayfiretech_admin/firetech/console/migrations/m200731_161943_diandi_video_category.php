<?php

use yii\db\Migration;

class m200731_161943_diandi_video_category extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_video_category}}', [
            'category_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id'",
            'store_id' => "int(11) NULL",
            'bloc_id' => "int(11) NULL",
            'name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称'",
            'parent_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类父id'",
            'thumb' => "varchar(250) NOT NULL COMMENT '分类图片'",
            'sort' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类排序'",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'update_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'PRIMARY KEY (`category_id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='分类管理'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10034','store_id'=>'43','bloc_id'=>'4','name'=>'洛川苹果','parent_id'=>'10033','thumb'=>'202003/10/3b2c68c3-e1a1-32a0-b183-ad1d92a384b3.jpg','sort'=>'1','create_time'=>'0','update_time'=>'0']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10037','store_id'=>'43','bloc_id'=>'4','name'=>'富平柿饼','parent_id'=>'10033','thumb'=>'','sort'=>'1','create_time'=>'0','update_time'=>'0']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10038','store_id'=>'43','bloc_id'=>'4','name'=>'蛋糕','parent_id'=>'10035','thumb'=>'','sort'=>'1','create_time'=>'0','update_time'=>'0']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10039','store_id'=>'43','bloc_id'=>'4','name'=>'甜点','parent_id'=>'10035','thumb'=>'','sort'=>'3','create_time'=>'0','update_time'=>'0']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10040','store_id'=>'43','bloc_id'=>'4','name'=>'照相机','parent_id'=>'0','thumb'=>'202007/12/46513df6-c836-377e-ab77-90c873d1e94d.jpg','sort'=>'1','create_time'=>'0','update_time'=>'1594552918']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10041','store_id'=>'43','bloc_id'=>'4','name'=>'照相机','parent_id'=>'10040','thumb'=>'202006/25/02430ba6-3956-3bf9-af21-0e0e0a1675aa.png','sort'=>'2','create_time'=>'0','update_time'=>'1593053867']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10044','store_id'=>'43','bloc_id'=>'4','name'=>'投影仪','parent_id'=>'0','thumb'=>'202007/12/dc5c6e95-f240-3f2b-b6d6-6bb37d8dd6c0.jpg','sort'=>'3','create_time'=>'0','update_time'=>'1594552946']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10045','store_id'=>'43','bloc_id'=>'4','name'=>'投影仪','parent_id'=>'10044','thumb'=>'202004/01/9ae70ef5-b078-3017-96e7-ae4394970606.jpg','sort'=>'4','create_time'=>'0','update_time'=>'0']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10046','store_id'=>'43','bloc_id'=>'4','name'=>'打印机','parent_id'=>'0','thumb'=>'202007/12/56fdcf71-377a-3d8a-831b-2546fc976cdb.jpg','sort'=>'4','create_time'=>'0','update_time'=>'1594552961']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10047','store_id'=>'43','bloc_id'=>'4','name'=>'打印机','parent_id'=>'10046','thumb'=>'202004/01/3e963eea-e5b9-308f-857b-abd9f6dcd6d8.jpg','sort'=>'6','create_time'=>'0','update_time'=>'0']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10048','store_id'=>'43','bloc_id'=>'4','name'=>'扫描仪','parent_id'=>'0','thumb'=>'202007/12/1aedd7ba-3198-3440-b1b1-810428b6dcd5.jpg','sort'=>'8','create_time'=>'0','update_time'=>'1594552977']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10049','store_id'=>'43','bloc_id'=>'4','name'=>'扫描仪','parent_id'=>'10048','thumb'=>'202004/01/09130816-1708-3b51-a187-548e32360070.jpg','sort'=>'9','create_time'=>'0','update_time'=>'0']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10051','store_id'=>'43','bloc_id'=>'4','name'=>'67','parent_id'=>'10050','thumb'=>'','sort'=>'6','create_time'=>'1593057231','update_time'=>'1593057231']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10053','store_id'=>'43','bloc_id'=>'4','name'=>'子类a','parent_id'=>'10052','thumb'=>'202007/07/b8626bfe-0e8b-38d6-adea-639915c5a881.jpg','sort'=>'1','create_time'=>'1594098244','update_time'=>'1594098244']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10054','store_id'=>'43','bloc_id'=>'4','name'=>'子类b','parent_id'=>'10052','thumb'=>'202007/07/bfc6907e-c5ac-3728-9ec8-3587aadb33e7.png','sort'=>'1','create_time'=>'1594098298','update_time'=>'1594098298']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10056','store_id'=>'43','bloc_id'=>'4','name'=>'子类1','parent_id'=>'10055','thumb'=>'202007/07/eefcf5ae-5059-3815-a74c-9ad743e84afa.png','sort'=>'1','create_time'=>'1594098403','update_time'=>'1594098403']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10057','store_id'=>'43','bloc_id'=>'4','name'=>'智能穿戴','parent_id'=>'10058','thumb'=>'202007/13/13314837-7d99-3894-b761-e2b632770b8d.png','sort'=>'6','create_time'=>'1594602852','update_time'=>'1594603438']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10058','store_id'=>'43','bloc_id'=>'4','name'=>'智能穿戴','parent_id'=>'0','thumb'=>'202007/13/0d786bac-91d8-3892-91c0-bf710f534d6f.png','sort'=>'0','create_time'=>'1594603394','update_time'=>'1594603394']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10059','store_id'=>'43','bloc_id'=>'4','name'=>'健康家居','parent_id'=>'0','thumb'=>'202007/13/45a123a2-b41f-3701-b05d-b214a6147ce8.jpg','sort'=>'7','create_time'=>'1594613664','update_time'=>'1594613664']);
        $this->insert('{{%diandi_video_category}}',['category_id'=>'10060','store_id'=>'43','bloc_id'=>'4','name'=>'枕头','parent_id'=>'10059','thumb'=>'202007/13/9973c8d5-1f62-31d8-967d-75f5eaadf3d5.jpg','sort'=>'0','create_time'=>'1594613707','update_time'=>'1594613707']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_video_category}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

