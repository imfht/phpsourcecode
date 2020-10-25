<?php
/*
 *
 * sysmanage.updatedata  升级数据
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */


class UpgradeData extends Action
{
    private $cacheDir = '';//缓存目录
    public function __construct()
    {
        _instance('Action/sysmanage/Auth');
        $this->file = _instance('Extend/File');
        $this->zip = _instance('Extend/Zip');
    }


    /**数据库升级
     * Author: lingqifei created by at 2020/5/17 0017
     */
    public function update_data_sql(){
        $sqlArr  =array();

        //添加记录可以重复执行
        //$s[]="INSERT ignore INTO `fly_sys_menu` (`id`, `name`, `name_en`, `url`, `parentID`, `sort`, `visible`) VALUES (614, '支付工具', 'PayTools', 'PayTools', 0, 7, 1);";
        //修改字段
       /// $s[]="alter table radacct_count_user modify tmp_sessiontime bigint(20)  default 0;";
        //增加字段
//        $sql = "SELECT table_name, column_name from information_schema.columns  WHERE table_name = 'fly_tp_package' and column_name LIKE 'sort';";
//        $res = $this->C($this->cacheDir)->countRecords($sql);
//        if(empty($res)){
//            $s[]="ALTER TABLE fly_tp_package ADD sort int(4) default 0;";
//        }
//
//        //创建表格
//        $s[]="
//                CREATE TABLE IF NOT EXISTS `fly_config_alipay` (
//                  `id` int(11) NOT NULL auto_increment,
//                  `name` varchar(256) NOT NULL,
//                  `value` text NOT NULL,
//                  PRIMARY KEY  (`id`)
//                ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
//		";

        $sqlArr[]="delete from fly_sys_config";
        $sqlArr[]="
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (1, '系统域名 ', 'basehost', 'http://www.07fly.top', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (2, '系统标题', 'title', '首页', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (3, '系统名称', 'name', '零起飞客户关系管理系统-07fly-CRM ', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (4, '系统版权', 'powerby', '网站版信息', 'bstring', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (5, '公司名称', 'companyname', '成都零起飞网络', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (6, '公司简介', 'companydesc', '<h4>零起飞介绍:</h4><p>服务项目：网站建设，域名空间，优化排名，网站推广，网站维护，软件定制等业务。 我们是技术性团队，只用作品说话。</p><p>服务宗旨：以质量求生存，以服务谋发展，以信誉创品牌</p><h4><br/></h4><h4>零起飞开源项目:</h4><p><a href=\"https://gitee.com/07fly/FLY-CRM\" target=\"_blank\">客户关系管理系统-（07FLY-CRM）</a><a href=\"//shang.qq.com/wpa/qunwpa?idkey=b587b0c97d7a7e17b805c05f5c2e4aa1a2a16958edee01c2d5208ac675e6d4aa\" target=\"_blank\">(QQ)交流群：575085787</a></p><p><a href=\"https://gitee.com/07fly/lingqifei\" target=\"_blank\">企业建站管理系统-（07FLY-CMS）</a><a href=\"//shang.qq.com/wpa/qunwpa?idkey=c7344a52e726be533fbdefe8cffd7f856d70ffe167afecb09c8cb0e27de731bf\" target=\"_blank\">(QQ)交流群：156729480</a></p><p><a href=\"https://gitee.com/07fly/07flyfms\" target=\"_blank\">小说网站管理系统-（07FLY-FMS）</a><a href=\"//shang.qq.com/wpa/qunwpa?idkey=630dd170e1779efe9edc5c24f08c0e9cac62524dc29cb3c711d21e88b18291d5\" target=\"_blank\">(QQ)交流群：326456035</a></p><p><a href=\"https://gitee.com/07fly/FLY-WEBOS\" target=\"_blank\">桌面应用框架系统-（WebSystem）</a> <a href=\"//shang.qq.com/wpa/qunwpa?idkey=55cf781a3aa2a259af48372f5ae3db00e82eae519e1140ec3e049e720fc2ea4a\" target=\"_blank\">(QQ)交流群：201192371</a></p><p><a href=\"http://bbs.zm-kj.com/forum-78-1.html\" target=\"_blank\">宽带认证计费系统-（AAARadius）</a> <a href=\"//shang.qq.com/wpa/qunwpa?idkey=6d5c31325e3168ef9cd16ea624fb2959e27eacdd4b06dfd4f240c13ce59f79ba\" target=\"_blank\">(QQ)交流群：125444118</a></p><h4><br/></h4><h4>有偿服务请联系:</h4><p>定制化开发,公司培训,技术支持,解决使用过程中出现的全部疑难问题</p><p>开发团队：零起飞网络</p><p>合作电话：18030402705(李先生)</p><p>技术支持：goodmuzi@qq.com</p><h4><br/></h4><h4>有限担保和免责声明:</h4><p>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。\r\n &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <br/></p><p>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，\r\n &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <br/></p><p>我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。\r\n &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <br/></p><p>究相关责任的权力。</p>', 'text', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (7, '联系电话', 'phone', '18030402705', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (8, '联系地址', 'address', '四川省成都市量力钢材城贸易区A4-3', 'string', 0);
        ";
        if(!empty($sqlArr)){
            foreach ( $sqlArr as $sql ) {
                $this->C( $this->cacheDir )->update( $sql );
            }
        }

    }

    /**数据库升级
     * Author: lingqifei created by at 2020/5/17 0017
     */
    public function update_data_init(){
        $sqlArr  =array();

        $sqlArr[]="delete from fly_sys_config";
        $sqlArr[]="
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (1, '系统域名 ', 'basehost', 'http://www.07fly.top', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (2, '系统标题', 'title', '首页', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (3, '系统名称', 'name', '零起飞客户关系管理系统-07fly-CRM ', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (4, '系统版权', 'powerby', '网站版信息', 'bstring', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (5, '公司名称', 'companyname', '成都零起飞网络', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (6, '公司简介', 'companydesc', '<h4>零起飞介绍:</h4><p>服务项目：网站建设，域名空间，优化排名，网站推广，网站维护，软件定制等业务。 我们是技术性团队，只用作品说话。</p><p>服务宗旨：以质量求生存，以服务谋发展，以信誉创品牌</p><h4><br/></h4><h4>零起飞开源项目:</h4><p><a href=\"https://gitee.com/07fly/FLY-CRM\" target=\"_blank\">客户关系管理系统-（07FLY-CRM）</a><a href=\"//shang.qq.com/wpa/qunwpa?idkey=b587b0c97d7a7e17b805c05f5c2e4aa1a2a16958edee01c2d5208ac675e6d4aa\" target=\"_blank\">(QQ)交流群：575085787</a></p><p><a href=\"https://gitee.com/07fly/lingqifei\" target=\"_blank\">企业建站管理系统-（07FLY-CMS）</a><a href=\"//shang.qq.com/wpa/qunwpa?idkey=c7344a52e726be533fbdefe8cffd7f856d70ffe167afecb09c8cb0e27de731bf\" target=\"_blank\">(QQ)交流群：156729480</a></p><p><a href=\"https://gitee.com/07fly/07flyfms\" target=\"_blank\">小说网站管理系统-（07FLY-FMS）</a><a href=\"//shang.qq.com/wpa/qunwpa?idkey=630dd170e1779efe9edc5c24f08c0e9cac62524dc29cb3c711d21e88b18291d5\" target=\"_blank\">(QQ)交流群：326456035</a></p><p><a href=\"https://gitee.com/07fly/FLY-WEBOS\" target=\"_blank\">桌面应用框架系统-（WebSystem）</a> <a href=\"//shang.qq.com/wpa/qunwpa?idkey=55cf781a3aa2a259af48372f5ae3db00e82eae519e1140ec3e049e720fc2ea4a\" target=\"_blank\">(QQ)交流群：201192371</a></p><p><a href=\"http://bbs.zm-kj.com/forum-78-1.html\" target=\"_blank\">宽带认证计费系统-（AAARadius）</a> <a href=\"//shang.qq.com/wpa/qunwpa?idkey=6d5c31325e3168ef9cd16ea624fb2959e27eacdd4b06dfd4f240c13ce59f79ba\" target=\"_blank\">(QQ)交流群：125444118</a></p><h4><br/></h4><h4>有偿服务请联系:</h4><p>定制化开发,公司培训,技术支持,解决使用过程中出现的全部疑难问题</p><p>开发团队：零起飞网络</p><p>合作电话：18030402705(李先生)</p><p>技术支持：goodmuzi@qq.com</p><h4><br/></h4><h4>有限担保和免责声明:</h4><p>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。\r\n &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <br/></p><p>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，\r\n &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <br/></p><p>我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。\r\n &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <br/></p><p>究相关责任的权力。</p>', 'text', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (7, '联系电话', 'phone', '18030402705', 'string', 0);
            REPLACE INTO `fly_sys_config` (`id`, `name`, `varname`, `value`, `type`, `groupid`) VALUES (8, '联系地址', 'address', '四川省成都市量力钢材城贸易区A4-3', 'string', 0);
        ";
        if(!empty($sqlArr)){
            foreach ( $sqlArr as $sql ) {
                $this->C( $this->cacheDir )->update( $sql );
            }
        }

    }


}//end class
?>