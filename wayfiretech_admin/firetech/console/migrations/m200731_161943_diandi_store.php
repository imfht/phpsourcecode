<?php

use yii\db\Migration;

class m200731_161943_diandi_store extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_store}}', [
            'store_id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '商户id'",
            'name' => "varchar(255) NULL COMMENT '门店名称'",
            'logo' => "varchar(255) NULL",
            'bloc_id' => "int(11) NULL COMMENT '关联公司'",
            'province' => "varchar(10) NULL COMMENT '省份'",
            'city' => "varchar(10) NULL COMMENT '城市'",
            'address' => "varchar(255) NULL COMMENT '详细地址'",
            'county' => "varchar(10) NULL COMMENT '区县'",
            'mobile' => "varchar(11) NULL COMMENT '联系电话'",
            'create_time' => "varchar(30) NULL",
            'update_time' => "varchar(30) NULL",
            'status' => "int(10) NULL DEFAULT '0' COMMENT '0:待审核,1:已通过,3:已拉黑'",
            'lng_lat' => "varchar(100) NULL COMMENT '经纬度'",
            'extra' => "text NULL COMMENT '商户扩展字段'",
            'PRIMARY KEY (`store_id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_store}}',['store_id'=>'27','name'=>'商户1','logo'=>'202005/16/3597fcdf-ba7b-36eb-a749-a14b7f493079.jpg','bloc_id'=>'0','province'=>'1532','city'=>'1674','address'=>'详细地址','county'=>'1677','mobile'=>'17778984690','create_time'=>NULL,'update_time'=>NULL,'status'=>'2','lng_lat'=>'116.456270,39.919990','extra'=>'a:14:{s:5:"intro";s:12:"简介内容";s:8:"shareimg";s:50:"202005/16/522e9b41-07b0-3c09-bb1a-97eab2c178da.jpg";s:8:"distance";s:3:"100";s:8:"sendtime";s:9:"5:00-6:00";s:13:"startingPrice";s:2:"12";s:12:"shippingDees";s:2:"16";s:6:"banner";s:50:"202005/16/1483b20a-c4e8-3423-971f-459431f6e795.jpg";s:8:"Lodop_ip";s:9:"127.0.0.1";s:7:"service";s:12:"服务内容";s:9:"hotSearch";s:6:"热狗";s:6:"notice";s:12:"公告内容";s:3:"des";s:12:"详细介绍";s:12:"surroundings";a:1:{i:0;s:50:"202005/12/873a0bbf-0ad3-35e4-91d2-bc205cbb6efb.jpg";}s:11:"certificate";a:2:{i:0;s:50:"202005/12/5b72fe31-e3b5-31ad-82d5-2d4878fcfcc6.jpg";i:1;s:50:"202005/12/a9a7c926-791d-344e-bb61-e4db7611aa40.png";}}']);
        $this->insert('{{%diandi_store}}',['store_id'=>'28','name'=>'商户A1','logo'=>'202005/12/c663ffe4-6a8a-3273-a92b-295776711313.jpg','bloc_id'=>'3','province'=>'220','city'=>'232','address'=>'详细地址','county'=>'233','mobile'=>'17778984690','create_time'=>NULL,'update_time'=>NULL,'status'=>'1','lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"116.456270\\\\\",\\\\\"lat\\\\\":\\\\\"39.919990\\\\\"}','extra'=>'b:0;']);
        $this->insert('{{%diandi_store}}',['store_id'=>'29','name'=>'商户2-3','logo'=>'202005/12/b60d42a7-5bff-33a2-a577-7947cb932a63.jpg','bloc_id'=>'3','province'=>'37','city'=>'76','address'=>'567','county'=>'78','mobile'=>'5454464','create_time'=>NULL,'update_time'=>NULL,'status'=>'2','lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"116.456270\\\\\",\\\\\"lat\\\\\":\\\\\"39.919990\\\\\"}','extra'=>'a:14:{s:5:"intro";s:0:"";s:8:"shareimg";s:0:"";s:8:"distance";s:0:"";s:8:"sendtime";s:0:"";s:13:"startingPrice";s:0:"";s:12:"shippingDees";s:0:"";s:6:"banner";s:0:"";s:8:"Lodop_ip";s:0:"";s:7:"service";s:0:"";s:9:"hotSearch";s:0:"";s:6:"notice";s:0:"";s:3:"des";s:0:"";s:12:"surroundings";s:0:"";s:11:"certificate";s:0:"";}']);
        $this->insert('{{%diandi_store}}',['store_id'=>'30','name'=>'商户134','logo'=>'202005/16/3597fcdf-ba7b-36eb-a749-a14b7f493079.jpg','bloc_id'=>'0','province'=>'19','city'=>'20','address'=>'详细地址','county'=>'21','mobile'=>'17778984690','create_time'=>NULL,'update_time'=>NULL,'status'=>'1','lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"116.456270\\\\\",\\\\\"lat\\\\\":\\\\\"39.919990\\\\\"}','extra'=>'b:0;']);
        $this->insert('{{%diandi_store}}',['store_id'=>'31','name'=>'新的测试','logo'=>'202005/16/83c5dd53-0241-3669-a9e1-47f513433989.png','bloc_id'=>'0','province'=>'2898','city'=>'2899','address'=>'567','county'=>'2900','mobile'=>'17778984690','create_time'=>NULL,'update_time'=>NULL,'status'=>'1','lng_lat'=>'116.456270,39.919990','extra'=>NULL]);
        $this->insert('{{%diandi_store}}',['store_id'=>'32','name'=>'途火科技1','logo'=>'202005/16/3597fcdf-ba7b-36eb-a749-a14b7f493079.jpg','bloc_id'=>'3','province'=>'1','city'=>'2','address'=>'详细地址','county'=>'7','mobile'=>'17778984690','create_time'=>NULL,'update_time'=>NULL,'status'=>'1','lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"116.456270\\\\\",\\\\\"lat\\\\\":\\\\\"39.919990\\\\\"}','extra'=>'b:0;']);
        $this->insert('{{%diandi_store}}',['store_id'=>'35','name'=>'经纬度格式','logo'=>'202005/16/3597fcdf-ba7b-36eb-a749-a14b7f493079.jpg','bloc_id'=>'0','province'=>'220','city'=>'232','address'=>'详细地址','county'=>'234','mobile'=>'5454464','create_time'=>NULL,'update_time'=>NULL,'status'=>NULL,'lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"116.456270\\\\\",\\\\\"lat\\\\\":\\\\\"39.919990\\\\\"}','extra'=>'a:14:{s:5:"intro";s:0:"";s:8:"shareimg";s:0:"";s:8:"distance";s:0:"";s:8:"sendtime";s:0:"";s:13:"startingPrice";s:0:"";s:12:"shippingDees";s:0:"";s:6:"banner";s:0:"";s:8:"Lodop_ip";s:0:"";s:7:"service";s:0:"";s:9:"hotSearch";s:0:"";s:6:"notice";s:0:"";s:3:"des";s:0:"";s:12:"surroundings";s:0:"";s:11:"certificate";s:0:"";}']);
        $this->insert('{{%diandi_store}}',['store_id'=>'38','name'=>'抽奖','logo'=>'202007/07/b7446834-ac1e-3e27-9d31-1006baeff8b2.jpg','bloc_id'=>'1','province'=>'820','city'=>'906','address'=>'详细地址','county'=>'911','mobile'=>'17778984690','create_time'=>NULL,'update_time'=>NULL,'status'=>'2','lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"108.946429\\\\\",\\\\\"lat\\\\\":\\\\\"34.347336\\\\\"}','extra'=>'a:15:{s:5:"intro";s:0:"";s:12:"contact_type";s:0:"";s:8:"shareimg";s:0:"";s:8:"distance";s:0:"";s:8:"sendtime";s:0:"";s:13:"startingPrice";s:0:"";s:12:"shippingDees";s:0:"";s:6:"banner";s:0:"";s:8:"Lodop_ip";s:0:"";s:7:"service";s:0:"";s:9:"hotSearch";s:30:"轻食餐,礼盒,枸杞,茶叶";s:6:"notice";s:0:"";s:3:"des";s:0:"";s:12:"surroundings";s:0:"";s:11:"certificate";s:0:"";}']);
        $this->insert('{{%diandi_store}}',['store_id'=>'43','name'=>'长全大健康','logo'=>'202007/07/3a3b5141-9245-3028-b076-5b30b67d4edd.png','bloc_id'=>'4','province'=>'37','city'=>'61','address'=>'详细地址','county'=>'62','mobile'=>'17778984690','create_time'=>NULL,'update_time'=>NULL,'status'=>'1','lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"109.339442\\\\\",\\\\\"lat\\\\\":\\\\\"34.14549\\\\\"}','extra'=>'a:19:{s:5:"intro";s:20:"上班下班都有Ta";s:12:"contact_type";s:1:"2";s:8:"shareimg";s:0:"";s:8:"distance";s:1:"5";s:8:"sendtime";s:23:"12:00-13:00,17:00-18:00";s:13:"startingPrice";s:2:"20";s:12:"shippingDees";s:1:"0";s:6:"banner";s:0:"";s:8:"Lodop_ip";s:9:"127.0.0.1";s:7:"service";s:12:"商家提供";s:9:"hotSearch";s:33:"打印机,笔记本,鼠标,键盘";s:6:"notice";s:60:"安全第一、客户至上、品质为重、服务至臻。";s:4:"USER";s:0:"";s:4:"UKEY";s:0:"";s:2:"SN";s:0:"";s:8:"printNum";s:0:"";s:3:"des";s:0:"";s:12:"surroundings";s:0:"";s:11:"certificate";s:0:"";}']);
        $this->insert('{{%diandi_store}}',['store_id'=>'44','name'=>'方创电子','logo'=>'202005/29/7b29556a-4b5b-3d40-9e00-7e52916f79a1.png','bloc_id'=>'5','province'=>'1532','city'=>'1674','address'=>'详细地址','county'=>'1681','mobile'=>'18729404118','create_time'=>NULL,'update_time'=>NULL,'status'=>'1','lng_lat'=>'{\\\\\"lng\\\\\":\\\\\"109.10788\\\\\",\\\\\"lat\\\\\":\\\\\"34.177242\\\\\"}','extra'=>'N;']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_store}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

