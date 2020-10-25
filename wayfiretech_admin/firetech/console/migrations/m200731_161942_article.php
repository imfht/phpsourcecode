<?php

use yii\db\Migration;

class m200731_161942_article extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%article}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'ishot' => "tinyint(1) unsigned NOT NULL",
            'pcate' => "int(10) unsigned NOT NULL",
            'ccate' => "int(10) unsigned NOT NULL",
            'template' => "varchar(300) NOT NULL",
            'title' => "varchar(100) NOT NULL",
            'description' => "varchar(100) NOT NULL",
            'content' => "mediumtext NOT NULL",
            'thumb' => "varchar(255) NOT NULL",
            'incontent' => "tinyint(1) NOT NULL",
            'source' => "varchar(255) NOT NULL",
            'author' => "varchar(50) NOT NULL",
            'displayorder' => "int(10) unsigned NOT NULL",
            'linkurl' => "varchar(500) NOT NULL",
            'createtime' => "int(10) unsigned NOT NULL",
            'edittime' => "int(10) NOT NULL",
            'click' => "int(10) unsigned NOT NULL",
            'type' => "varchar(10) NOT NULL",
            'credit' => "varchar(255) NOT NULL",
            'icon' => "varchar(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文章资讯'");
        
        /* 索引设置 */
        $this->createIndex('idx_ishot','{{%article}}','ishot',0);
        
        
        /* 表数据 */
        $this->insert('{{%article}}',['id'=>'1','ishot'=>'0','pcate'=>'10','ccate'=>'13','template'=>'8','title'=>'智能硬件','description'=>'提供专业的智能硬件推荐方案，包括人脸抓拍机、人流热力图机等，实现在前端设备上运行人脸抓拍、人体检测跟踪等AI能力','content'=>'<p>提供专业的智能硬件推荐方案，包括人脸抓拍机、人流热力图机等，实现在前端设备上运行人脸抓拍、人体检测跟踪等AI能力</p>','thumb'=>'202002/29/516422b5-c302-3e95-b7e8-9c2038cd8be9.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1582972566','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-microchip']);
        $this->insert('{{%article}}',['id'=>'2','ishot'=>'0','pcate'=>'10','ccate'=>'13','template'=>'jieshao','title'=>'顾客洞察','description'=>'提供人脸识别与人体分析的云端服务，实现顾客个体识别、人体跟踪以及顾客群体画像与重复到店识别等分析功能','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">提供人脸识别与人体分析的云端服务，实现顾客个体识别、人体跟踪以及顾客群体画像与重复到店识别等分析功能</span></p>','thumb'=>'upload/202001/18/e0b8ea3c-3176-3c86-b515-8961e9b52531.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579318080','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-user-plus ']);
        $this->insert('{{%article}}',['id'=>'3','ishot'=>'0','pcate'=>'10','ccate'=>'13','template'=>'jieshao','title'=>'店铺管理模块','description'=>'从门店客流、顾客回访率、顾客停留时长及消费转化率、店员考勤监控等角度，实现对店铺的有效管理','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">从门店客流、顾客回访率、顾客停留时长及消费转化率、店员考勤监控等角度，实现对店铺的有效管理</span></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579318696','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-home ']);
        $this->insert('{{%article}}',['id'=>'4','ishot'=>'0','pcate'=>'10','ccate'=>'11','template'=>'jieshao','title'=>'人脸检测定位','description'=>'检测图片中的人脸并标记出人脸坐标，支持同时识别多张人脸','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">检测图片中的人脸并标记出人脸坐标，支持同时识别多张人脸</span></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320120','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-user-o']);
        $this->insert('{{%article}}',['id'=>'5','ishot'=>'0','pcate'=>'10','ccate'=>'11','template'=>'jieshao','title'=>'人脸属性分析','description'=>'准确识别多种人脸属性信息，包括年龄、性别、颜值、表情、情绪、脸型、头部姿态、是否闭眼、是否配戴眼镜、人脸质量信息及类型等','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">准确识别多种人脸属性信息，包括年龄、性别、颜值、表情、情绪、脸型、头部姿态、是否闭眼、是否配戴眼镜、人脸质量信息及类型等</span></p>','thumb'=>'upload/202001/18/d0745841-7b4e-3726-a323-a6d64d742f5b.jpg','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320162','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-microchip']);
        $this->insert('{{%article}}',['id'=>'6','ishot'=>'0','pcate'=>'10','ccate'=>'11','template'=>'jieshao','title'=>'150关键点定位','description'=>'精准定位包括脸颊、眉、眼、口、鼻等人脸五官及轮廓的150个关键点','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">精准定位包括脸颊、眉、眼、口、鼻等人脸五官及轮廓的150个关键点</span></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320195','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-microchip']);
        $this->insert('{{%article}}',['id'=>'7','ishot'=>'0','pcate'=>'10','ccate'=>'11','template'=>'jieshao','title'=>'情绪识别','description'=>'分析检测到的人脸的情绪，并返回置信度分数，目前可识别愤怒、厌恶、恐惧、高兴、伤心、惊讶、嘟嘴、鬼脸、无情绪等9种情绪','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">分析检测到的人脸的情绪，并返回置信度分数，目前可识别愤怒、厌恶、恐惧、高兴、伤心、惊讶、嘟嘴、鬼脸、无情绪等9种情绪</span></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320229','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-microchip']);
        $this->insert('{{%article}}',['id'=>'8','ishot'=>'0','pcate'=>'10','ccate'=>'11','template'=>'jieshao','title'=>'图片质量控制','description'=>'分析图片中人脸的遮挡度、模糊度、光照强度、姿态角度、完整度、大小等特征，确保图片符合质量标准，保障后续人脸对比、搜索的准确性','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">分析图片中人脸的遮挡度、模糊度、光照强度、姿态角度、完整度、大小等特征，确保图片符合质量标准，保障后续人脸对比、搜索的准确性</span></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320258','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-microchip']);
        $this->insert('{{%article}}',['id'=>'9','ishot'=>'0','pcate'=>'10','ccate'=>'11','template'=>'jieshao','title'=>'在线图片活体检测','description'=>'基于单张图片中人像的破绽（摩尔纹、成像畸形等），判断图片是否为二次翻拍，过滤检测中不符合标准的人脸','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">基于单张图片中人像的破绽（摩尔纹、成像畸形等），判断图片是否为二次翻拍，过滤检测中不符合标准的人脸</span></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320284','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-microchip']);
        $this->insert('{{%article}}',['id'=>'10','ishot'=>'0','pcate'=>'10','ccate'=>'9','template'=>'jieshao','title'=>'商场客流统计','description'=>'商场的客流量对于运营情况分析是十分重要的数据，通过抓拍机对商场内的顾客人脸进行实时抓拍识别，能够统计到精准的区中顾客数；通过人流热力图机与云端算法的配合对商场客流热力分布图进行绘制，最直观地让运营者了','content'=>'<p>商场的客流量对于运营情况分析是十分重要的数据，通过抓拍机对商场内的顾客人脸进行实时抓拍识别，能够统计到精准的区中顾客数；通过人流热力图机与云端算法的配合对商场客流热力分布图进行绘制，最直观地让运营者了解商场运营状态进而做出更准确的经营决策。</p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320863','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-home ']);
        $this->insert('{{%article}}',['id'=>'11','ishot'=>'0','pcate'=>'10','ccate'=>'9','template'=>'jieshao','title'=>'门店智能管理','description'=>'通过人脸检测抓拍、和库中人脸比对的方式获取来人身份信息，尤其应用于新零售场景中，可用于识别门店会员，与系统中的会员购买记录、联系方式等信息匹配，推送给店员，实现精准营销。','content'=>'<p><span style=\\\\\"font-family: BlinkMacSystemFont, Roboto, &quot;Helvetica Neue&quot;, Helvetica, PingFangSC-Regular, &quot;Hiragino Sans GB&quot;, &quot;Microsoft YaHei&quot;, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\\\\\">通过人脸检测抓拍、和库中人脸比对的方式获取来人身份信息，尤其应用于新零售场景中，可用于识别门店会员，与系统中的会员购买记录、联系方式等信息匹配，推送给店员，实现精准营销。</span></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579320892','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa-user-plus ']);
        $this->insert('{{%article}}',['id'=>'12','ishot'=>'0','pcate'=>'10','ccate'=>'9','template'=>'jieshao','title'=>'店内监控','description'=>'店内监控','content'=>'<p>店内监控</p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579321283','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa-user-plus ']);
        $this->insert('{{%article}}',['id'=>'13','ishot'=>'0','pcate'=>'10','ccate'=>'8','template'=>'jieshao','title'=>'人脸识别摄像头','description'=>'人脸识别摄像头','content'=>'<p>人脸识别摄像头</p>','thumb'=>'upload/202001/18/707700ac-e089-32c1-854c-57ee6d7ab838.jpg','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579322166','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa-microchip']);
        $this->insert('{{%article}}',['id'=>'14','ishot'=>'0','pcate'=>'10','ccate'=>'12','template'=>'jieshao','title'=>'接口开源','description'=>'接口完全开源','content'=>'<p>接口文档开源：<a href=\\\\\"http://www.open.com/index.php?r=doc\\\\\" target=\\\\\"_blank\\\\\" title=\\\\\"查看接口文档\\\\\">文档地址</a></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579404291','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa fa-user-o']);
        $this->insert('{{%article}}',['id'=>'15','ishot'=>'0','pcate'=>'10','ccate'=>'12','template'=>'jieshao','title'=>'代码开源','description'=>'代码开源','content'=>'<p>代码开源：<a href=\\\\\"https://gitee.com/wayfiretech_admin/firetech\\\\\" target=\\\\\"_blank\\\\\">源码GIT地址</a></p>','thumb'=>'upload/202001/18/9877f497-97ac-3f4b-84cf-6563c059162f.png','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'https://gitee.com/wayfiretech_admin/firetech','createtime'=>'1579404417','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'fa-microchip']);
        $this->insert('{{%article}}',['id'=>'16','ishot'=>'0','pcate'=>'10','ccate'=>'14','template'=>'jieshao','title'=>'店滴AI','description'=>'基于人脸识别的会员管理系统','content'=>'<p>基于人脸识别的会员管理系统</p>','thumb'=>'upload/202001/19/b7440d3b-f625-34c2-9851-3f3a983baa53.jpg','incontent'=>'0','source'=>'wu','author'=>'diandi','displayorder'=>'1','linkurl'=>'www','createtime'=>'1579400356','edittime'=>'0','click'=>'0','type'=>'','credit'=>'','icon'=>'']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%article}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

