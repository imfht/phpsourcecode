<?php

namespace Addons\InsertTopic;

use Common\Controller\Addon;

/**
 * 插入附件插件
 * @author onep2p
 */
class InsertTopicAddon extends Addon
{

    public $info = array(
        'name' => 'InsertTopic',
        'title' => '插入话题',
        'description' => '微博话题插件',
        'status' => 1,
        'author' => 'onep2p',
        'version' => '0.1'
    );

    public function install()
    {
        $db_prefix = C('DB_PREFIX');
        $sql = <<<sql
CREATE TABLE IF NOT EXISTS `{$db_prefix}topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '话题名',
  `logo` varchar(255) NOT NULL DEFAULT '/topicavatar.jpg' COMMENT '话题logo',
  `intro` varchar(255) NOT NULL COMMENT '导语',
  `qrcode` varchar(255) NOT NULL COMMENT '二维码',
  `uadmin` int(11) NOT NULL DEFAULT '0' COMMENT '话题管理   默认无',
  `read_count` int(11) NOT NULL DEFAULT '0' COMMENT '阅读',
  `is_top` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
sql;
        D('')->execute($sql);
        return true;
    }

    public function uninstall()
    {
        $db_prefix = C('DB_PREFIX');
        $sql = <<<sql
        DROP TABLE IF EXISTS `{$db_prefix}topic`;
sql;
        D('')->execute($sql);
        return true;
    }

    //实现的InsertTopic钩子方法
    public function weiboType($param)
    {
        $this->display('InsertTopic');
    }

    public function beforeSendWeibo($param)
    {
        $this->beforeSendRepost($param);
    }

    //发送钩子
    public function beforeSendRepost($param)
    {
        //检测话题的存在性
        $topic = get_topic($param['content']);
        if (isset($topic) && !is_null($topic)) {
            foreach ($topic as $e) {
                $tik = D('Weibo/Topic')->where(array('name' => $e))->find();

                //没有这个话题的时候创建这个话题
                if (!$tik) {
                    D('Weibo/Topic')->add(array('name' => $e));
                }
            }
        }
    }
    public function parseWeiboContent($parm){

        $parm['content']= parse_topic($parm['content']);
    }

    //展示数据
    public function fetchTopic($weibo)
    {
        $weibo_data = unserialize($weibo['data']);

        $param['weibo'] = $weibo;
        $param['weibo']['weibo_data'] = $weibo_data;

        $this->assign($param);
        return $this->fetch('display');
    }

}