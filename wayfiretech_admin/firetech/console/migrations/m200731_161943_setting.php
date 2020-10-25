<?php

use yii\db\Migration;

class m200731_161943_setting extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%setting}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'cate_name' => "varchar(255) NULL",
            'type' => "varchar(10) NOT NULL",
            'section' => "varchar(255) NOT NULL",
            'key' => "varchar(255) NOT NULL",
            'store_id' => "int(11) NULL",
            'bloc_id' => "int(11) NULL",
            'value' => "text NOT NULL",
            'status' => "smallint(6) NOT NULL DEFAULT '1'",
            'description' => "varchar(255) NULL",
            'created_at' => "int(11) NOT NULL",
            'updated_at' => "int(11) NOT NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%setting}}',['id'=>'1','cate_name'=>NULL,'type'=>'string','section'=>'第一','key'=>'website','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'websitev','status'=>'0','description'=>'描述','created_at'=>'1579095354','updated_at'=>'1579095401']);
        $this->insert('{{%setting}}',['id'=>'2','cate_name'=>NULL,'type'=>'string','section'=>'ConfigurationForm','key'=>'appName','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'实施','status'=>'1','description'=>NULL,'created_at'=>'1579105859','updated_at'=>'1579105859']);
        $this->insert('{{%setting}}',['id'=>'3','cate_name'=>NULL,'type'=>'string','section'=>'ConfigurationForm','key'=>'adminEmail','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'你ni','status'=>'1','description'=>NULL,'created_at'=>'1579105859','updated_at'=>'1579106350']);
        $this->insert('{{%setting}}',['id'=>'4','cate_name'=>NULL,'type'=>'string','section'=>'Baidu','key'=>'APP_ID','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'18260702','status'=>'1','description'=>NULL,'created_at'=>'1579107090','updated_at'=>'1579224430']);
        $this->insert('{{%setting}}',['id'=>'5','cate_name'=>NULL,'type'=>'string','section'=>'Baidu','key'=>'API_KEY','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'euSGa9FaVg90vQZsTbIKxPnI','status'=>'1','description'=>NULL,'created_at'=>'1579107090','updated_at'=>'1579224430']);
        $this->insert('{{%setting}}',['id'=>'6','cate_name'=>NULL,'type'=>'string','section'=>'Baidu','key'=>'SECRET_KEY','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'dUhq1DzKOGO2BseeDaqGtQ4EhGto1PSq','status'=>'1','description'=>NULL,'created_at'=>'1579107091','updated_at'=>'1579224431']);
        $this->insert('{{%setting}}',['id'=>'7','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'status','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'0','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1579230151']);
        $this->insert('{{%setting}}',['id'=>'8','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'reason','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'99','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1579108394']);
        $this->insert('{{%setting}}',['id'=>'9','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'icp','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'陕ICP备13008470号-7','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'10','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'code','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'陕ICP备13008470号-7','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'11','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'location','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'www.wayfirer.com','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699033']);
        $this->insert('{{%setting}}',['id'=>'12','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'develop_status','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'0','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1579230151']);
        $this->insert('{{%setting}}',['id'=>'13','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'flogo','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'202004/12/edec15aa-705f-33e2-b699-db846345a779.png','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'14','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'blogo','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'202004/12/ecf871cc-1b3b-3163-abaf-bbe3b24311cf.png','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'15','cate_name'=>NULL,'type'=>'array','section'=>'Website','key'=>'slides','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'Array','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1579230151']);
        $this->insert('{{%setting}}',['id'=>'16','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'notice','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'幻灯片中的文字','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699033']);
        $this->insert('{{%setting}}',['id'=>'17','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'statcode','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'7','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1579108394']);
        $this->insert('{{%setting}}',['id'=>'18','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'footerright','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'基于AI的应用软硬件解决方案','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'19','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'footerleft','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'All right reserved to 店滴 ©2020','status'=>'1','description'=>NULL,'created_at'=>'1579108394','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'20','cate_name'=>NULL,'type'=>'string','section'=>'Baidu','key'=>'name','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'店滴AI','status'=>'1','description'=>NULL,'created_at'=>'1579224531','updated_at'=>'1579224531']);
        $this->insert('{{%setting}}',['id'=>'21','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'name','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'店滴AI应用开源系统','status'=>'1','description'=>NULL,'created_at'=>'1579230151','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'22','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'intro','store_id'=>NULL,'bloc_id'=>'1','value'=>'基于AI的软硬件应用解决方案','status'=>'1','description'=>NULL,'created_at'=>'1579232517','updated_at'=>'1587206089']);
        $this->insert('{{%setting}}',['id'=>'23','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'description','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'基于AI的餐饮应用软硬件解决方案，智慧餐饮系统','status'=>'1','description'=>NULL,'created_at'=>'1579232517','updated_at'=>'1586699033']);
        $this->insert('{{%setting}}',['id'=>'24','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'keywords','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'人脸识别,店滴AI、人工智能、人脸识别闸机','status'=>'1','description'=>NULL,'created_at'=>'1579232517','updated_at'=>'1586699592']);
        $this->insert('{{%setting}}',['id'=>'25','cate_name'=>NULL,'type'=>'string','section'=>'Weburl','key'=>'backendurl','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'http://www.ai.com/attachment','status'=>'1','description'=>NULL,'created_at'=>'1579234124','updated_at'=>'1582970996']);
        $this->insert('{{%setting}}',['id'=>'26','cate_name'=>NULL,'type'=>'string','section'=>'Weburl','key'=>'frendurl','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'http://www.ai.com','status'=>'1','description'=>NULL,'created_at'=>'1579234124','updated_at'=>'1582970935']);
        $this->insert('{{%setting}}',['id'=>'27','cate_name'=>NULL,'type'=>'string','section'=>'Weburl','key'=>'apiurl','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'http://www.ai.com/api','status'=>'1','description'=>NULL,'created_at'=>'1579234124','updated_at'=>'1582970935']);
        $this->insert('{{%setting}}',['id'=>'28','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'themcolor','store_id'=>NULL,'bloc_id'=>'3','value'=>'skin-purple-light','status'=>'1','description'=>NULL,'created_at'=>'1580734530','updated_at'=>'1589332225']);
        $this->insert('{{%setting}}',['id'=>'29','cate_name'=>NULL,'type'=>'string','section'=>'Sms','key'=>'access_key_id','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'LTAI3Vun6MH6MzyZ','status'=>'1','description'=>NULL,'created_at'=>'1581334350','updated_at'=>'1581334350']);
        $this->insert('{{%setting}}',['id'=>'30','cate_name'=>NULL,'type'=>'string','section'=>'Sms','key'=>'access_key_secret','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'tXI6xfEppupV8r0OKjkC93yBWAPu4V','status'=>'1','description'=>NULL,'created_at'=>'1581334350','updated_at'=>'1581334350']);
        $this->insert('{{%setting}}',['id'=>'31','cate_name'=>NULL,'type'=>'string','section'=>'Sms','key'=>'sign_name','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'店滴会员卡','status'=>'1','description'=>NULL,'created_at'=>'1581334350','updated_at'=>'1581334350']);
        $this->insert('{{%setting}}',['id'=>'32','cate_name'=>NULL,'type'=>'string','section'=>'Sms','key'=>'template_code','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'SMS_163645027','status'=>'1','description'=>NULL,'created_at'=>'1581334350','updated_at'=>'1581334350']);
        $this->insert('{{%setting}}',['id'=>'33','cate_name'=>NULL,'type'=>'string','section'=>'Wxapp','key'=>'name','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'有Ta平台','status'=>'1','description'=>NULL,'created_at'=>'1584120221','updated_at'=>'1585547826']);
        $this->insert('{{%setting}}',['id'=>'34','cate_name'=>NULL,'type'=>'string','section'=>'Wxapp','key'=>'description','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'店滴会员','status'=>'1','description'=>NULL,'created_at'=>'1584120221','updated_at'=>'1584120221']);
        $this->insert('{{%setting}}',['id'=>'35','cate_name'=>NULL,'type'=>'string','section'=>'Wxapp','key'=>'original','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'wx028eb56f4b4a7d99','status'=>'1','description'=>NULL,'created_at'=>'1584120221','updated_at'=>'1584120221']);
        $this->insert('{{%setting}}',['id'=>'36','cate_name'=>NULL,'type'=>'string','section'=>'Wxapp','key'=>'AppId','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'wx028eb56f4b4a7d99','status'=>'1','description'=>NULL,'created_at'=>'1584120221','updated_at'=>'1584120221']);
        $this->insert('{{%setting}}',['id'=>'37','cate_name'=>NULL,'type'=>'string','section'=>'Wxapp','key'=>'AppSecret','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'44f188b226d3c04c403d798d8963817b','status'=>'1','description'=>NULL,'created_at'=>'1584120221','updated_at'=>'1584120221']);
        $this->insert('{{%setting}}',['id'=>'38','cate_name'=>NULL,'type'=>'string','section'=>'Wxapp','key'=>'headimg','store_id'=>NULL,'bloc_id'=>'1','value'=>'202004/25/4c931202-71c6-3ea5-9e77-501823c05d89.jpg','status'=>'1','description'=>NULL,'created_at'=>'1584120221','updated_at'=>'1587813050']);
        $this->insert('{{%setting}}',['id'=>'39','cate_name'=>NULL,'type'=>'string','section'=>'Wechatpay','key'=>'mch_id','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'1228641802','status'=>'1','description'=>NULL,'created_at'=>'1584120542','updated_at'=>'1584120542']);
        $this->insert('{{%setting}}',['id'=>'40','cate_name'=>NULL,'type'=>'string','section'=>'Wechatpay','key'=>'app_id','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'wx028eb56f4b4a7d99','status'=>'1','description'=>NULL,'created_at'=>'1584120542','updated_at'=>'1584120542']);
        $this->insert('{{%setting}}',['id'=>'41','cate_name'=>NULL,'type'=>'string','section'=>'Wechatpay','key'=>'key','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'t2phkrvdglrunljg20vo3etlmtvzz1rp','status'=>'1','description'=>NULL,'created_at'=>'1584120542','updated_at'=>'1584120542']);
        $this->insert('{{%setting}}',['id'=>'42','cate_name'=>NULL,'type'=>'string','section'=>'Wechatpay','key'=>'notify_url','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'/api/wechat/basics/notify','status'=>'1','description'=>NULL,'created_at'=>'1584120542','updated_at'=>'1584122304']);
        $this->insert('{{%setting}}',['id'=>'52','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'mobile','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'18729404118','status'=>'1','description'=>NULL,'created_at'=>'1584606773','updated_at'=>'1585544971']);
        $this->insert('{{%setting}}',['id'=>'53','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'title','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'途火科技','status'=>'1','description'=>NULL,'created_at'=>'1584606773','updated_at'=>'1585858694']);
        $this->insert('{{%setting}}',['id'=>'54','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'startingPrice','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'20','status'=>'1','description'=>NULL,'created_at'=>'1584606774','updated_at'=>'1586440704']);
        $this->insert('{{%setting}}',['id'=>'55','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'intro','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'客户至上，服务至臻。','status'=>'1','description'=>NULL,'created_at'=>'1584606774','updated_at'=>'1585546337']);
        $this->insert('{{%setting}}',['id'=>'56','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'shippingDees','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'2','status'=>'1','description'=>NULL,'created_at'=>'1584606774','updated_at'=>'1585718931']);
        $this->insert('{{%setting}}',['id'=>'57','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'address','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'陕西省西安市高新区','status'=>'1','description'=>NULL,'created_at'=>'1584606774','updated_at'=>'1585731687']);
        $this->insert('{{%setting}}',['id'=>'58','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'describe','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'店滴AI应用开源系统-基于AI的应用软硬件解决方案。','status'=>'1','description'=>NULL,'created_at'=>'1584606774','updated_at'=>'1585731687']);
        $this->insert('{{%setting}}',['id'=>'59','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'logo','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'202004/01/8c8c8313-8b45-3efc-9cf2-598bd880b7e8.jpg','status'=>'1','description'=>NULL,'created_at'=>'1584606774','updated_at'=>'1585731687']);
        $this->insert('{{%setting}}',['id'=>'60','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'banner','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'202004/01/89555b8b-8723-3c19-8372-30afb792cdd6.jpg','status'=>'1','description'=>NULL,'created_at'=>'1584606774','updated_at'=>'1585731854']);
        $this->insert('{{%setting}}',['id'=>'61','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'storeId','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'20200401001','status'=>'1','description'=>NULL,'created_at'=>'1584653926','updated_at'=>'1585544971']);
        $this->insert('{{%setting}}',['id'=>'62','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'hotSearch','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'打印机、投影仪','status'=>'1','description'=>NULL,'created_at'=>'1584788010','updated_at'=>'1585731687']);
        $this->insert('{{%setting}}',['id'=>'63','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'notice','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'店滴AI基于人脸识别的软硬件开源系统','status'=>'1','description'=>NULL,'created_at'=>'1584975453','updated_at'=>'1585719418']);
        $this->insert('{{%setting}}',['id'=>'69','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'surroundings','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'202004/01/19df36cf-ab91-3f85-8fb1-3bc7f60c69e6.jpg,202004/01/f05469db-0983-3d17-8dfe-9b4450750267.jpg,202004/01/d4d1e2ab-6a8e-34ef-8789-a8580250416f.jpg,202004/01/d3cae280-532c-3274-aefc-d0f34f8ca9cc.jpg','status'=>'1','description'=>NULL,'created_at'=>'1585038195','updated_at'=>'1585731687']);
        $this->insert('{{%setting}}',['id'=>'70','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'certificate','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'202004/01/39285c22-ae10-3dcc-8145-f93a9bff7c9b.jpg,202004/01/b8262743-ca08-3350-a57a-d1c060a07c36.jpg,202004/01/4d106e54-1210-3f5b-b8b0-555c6b580439.jpg,202004/01/2029faa8-481d-3564-b416-03cf78dc122a.jpg','status'=>'1','description'=>NULL,'created_at'=>'1585039230','updated_at'=>'1585731687']);
        $this->insert('{{%setting}}',['id'=>'71','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'lng_lat','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'116.325884,40.126105','status'=>'1','description'=>NULL,'created_at'=>'1585065145','updated_at'=>'1585858673']);
        $this->insert('{{%setting}}',['id'=>'72','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'distance','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'500000','status'=>'1','description'=>NULL,'created_at'=>'1585067343','updated_at'=>'1586428244']);
        $this->insert('{{%setting}}',['id'=>'73','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'service','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'由商家提供配送服务','status'=>'1','description'=>NULL,'created_at'=>'1585499690','updated_at'=>'1585559097']);
        $this->insert('{{%setting}}',['id'=>'74','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'sendtime','store_id'=>NULL,'bloc_id'=>'1','value'=>'11:00-12:30,17:00-18:30','status'=>'1','description'=>NULL,'created_at'=>'1585499690','updated_at'=>'1587100767']);
        $this->insert('{{%setting}}',['id'=>'75','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'shareimg','store_id'=>NULL,'bloc_id'=>NULL,'value'=>'202004/03/0f58683e-056b-322a-aaf6-0be018d6efec.png','status'=>'1','description'=>NULL,'created_at'=>'1585884344','updated_at'=>'1585884344']);
        $this->insert('{{%setting}}',['id'=>'76','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStroe','key'=>'Lodop_ip','store_id'=>NULL,'bloc_id'=>'1','value'=>'127.0.0.1','status'=>'1','description'=>NULL,'created_at'=>'1587094770','updated_at'=>'1587811933']);
        $this->insert('{{%setting}}',['id'=>'77','cate_name'=>NULL,'type'=>'string','section'=>'BlocConfBaidu','key'=>'bloc_id','store_id'=>NULL,'bloc_id'=>'1','value'=>'3','status'=>'1','description'=>NULL,'created_at'=>'1588232757','updated_at'=>'1588232757']);
        $this->insert('{{%setting}}',['id'=>'78','cate_name'=>NULL,'type'=>'string','section'=>'BlocConfBaidu','key'=>'APP_ID','store_id'=>NULL,'bloc_id'=>'1','value'=>'2','status'=>'1','description'=>NULL,'created_at'=>'1588232757','updated_at'=>'1588232757']);
        $this->insert('{{%setting}}',['id'=>'79','cate_name'=>NULL,'type'=>'string','section'=>'BlocConfBaidu','key'=>'API_KEY','store_id'=>NULL,'bloc_id'=>'1','value'=>'34','status'=>'1','description'=>NULL,'created_at'=>'1588232757','updated_at'=>'1588232757']);
        $this->insert('{{%setting}}',['id'=>'80','cate_name'=>NULL,'type'=>'string','section'=>'BlocConfBaidu','key'=>'SECRET_KEY','store_id'=>NULL,'bloc_id'=>'1','value'=>'456','status'=>'1','description'=>NULL,'created_at'=>'1588232757','updated_at'=>'1588232757']);
        $this->insert('{{%setting}}',['id'=>'81','cate_name'=>NULL,'type'=>'string','section'=>'BlocConfBaidu','key'=>'name','store_id'=>NULL,'bloc_id'=>'1','value'=>'56','status'=>'1','description'=>NULL,'created_at'=>'1588232757','updated_at'=>'1588232757']);
        $this->insert('{{%setting}}',['id'=>'82','cate_name'=>NULL,'type'=>'string','section'=>'Website','key'=>'bloc_id','store_id'=>NULL,'bloc_id'=>'1','value'=>'0','status'=>'1','description'=>NULL,'created_at'=>'1588261515','updated_at'=>'1588261515']);
        $this->insert('{{%setting}}',['id'=>'83','cate_name'=>NULL,'type'=>'string','section'=>'DiandiShopStore','key'=>'storeId','store_id'=>NULL,'bloc_id'=>'1','value'=>'1','status'=>'1','description'=>NULL,'created_at'=>'1588270047','updated_at'=>'1588270047']);
        $this->insert('{{%setting}}',['id'=>'84','cate_name'=>NULL,'type'=>'string','section'=>'Map','key'=>'baiduApk','store_id'=>NULL,'bloc_id'=>'3','value'=>'sY7GGnljSvLzM44mEwVtGozS','status'=>'1','description'=>NULL,'created_at'=>'1589676971','updated_at'=>'1589688212']);
        $this->insert('{{%setting}}',['id'=>'85','cate_name'=>NULL,'type'=>'string','section'=>'Map','key'=>'amapApk','store_id'=>NULL,'bloc_id'=>'3','value'=>'2','status'=>'1','description'=>NULL,'created_at'=>'1589676972','updated_at'=>'1590386628']);
        $this->insert('{{%setting}}',['id'=>'86','cate_name'=>NULL,'type'=>'string','section'=>'Map','key'=>'tencentApk','store_id'=>NULL,'bloc_id'=>'3','value'=>'23','status'=>'1','description'=>NULL,'created_at'=>'1589676972','updated_at'=>'1590386628']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%setting}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

