<?php

namespace Addons\Mail;

use Common\Controller\Addon;
use Think\Db;

/**
 * 邮件订阅插件
 * @author quick
 */

class MailAddon extends Addon
{

    public $info = array(
        'name' => 'Mail',
        'title' => '邮件订阅',
        'description' => '邮件订阅插件',
        'status' => 1,
        'author' => 'xjw129xjt',
        'version' => '0.1.1'
    );

    public $addon_path = './Addons/Mail/';

    /**
     * 配置列表页面
     * @var unknown_type
     */
    public $admin_list = array(
        'model' => 'Config',
        'order' => 'find_in_set( name ,"MAIL_TYPE,MAIL_SMTP_HOST,MAIL_SMTP_PORT,MAIL_SMTP_USER,MAIL_SMTP_PASS,MAIL_SMTP_CE,WEB_SITE") ',
        'map' => array('name' => array('in', array(0 => 'MAIL_TYPE', 1 => 'MAIL_SMTP_HOST', 2 => 'MAIL_SMTP_PORT', 3 => 'MAIL_SMTP_USER', 4 => 'MAIL_SMTP_PASS', 5 => 'MAIL_SMTP_CE', 6 => 'WEB_SITE')))
    );
    public $custom_adminlist = 'adminlist.html';

    /**
     * (non-PHPdoc)
     * 安装函数
     * @see \Common\Controller\Addons::install()
     */
    public function table_name()
    {
        $db_prefix = C('DB_PREFIX');
        return $db_prefix;
    }


    public function install()
    {
        $prefix = C("DB_PREFIX");
        $model = D();
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}mail_history;");
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}history_link;");
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}mail_list;");
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}mail_token;");


        $model->execute(<<<SQL
CREATE TABLE IF NOT EXISTS `{$this->table_name()}mail_history` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `from` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
        );

            $model->execute(<<<SQL
   CREATE TABLE IF NOT EXISTS `{$this->table_name()}mail_history_link` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) NOT NULL,
  `to` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
        );

        $model->execute(<<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->table_name()}mail_list` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
        );
        $model->execute(<<<SQL
CREATE TABLE IF NOT EXISTS `{$this->table_name()}mail_token` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `token` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
        );


        return true;
    }

    /**
     * (non-PHPdoc)
     * 卸载函数
     * @see \Common\Controller\Addons::uninstall()
     */
    public function uninstall()
    {
        $model = D();
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}mail_history;");
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}mail_history_link;");
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}mail_list;");
        $model->execute("DROP TABLE IF EXISTS {$this->table_name()}mail_token;");
        return true;
    }

    //实现的钩子
    public function AdminIndex($param)
    {

    }


    public function pageFooter(){
        $this->display('subscribe');
    }
}