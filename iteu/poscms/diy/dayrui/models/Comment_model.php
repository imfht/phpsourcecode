<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Comment_model extends CI_Model {

    public $mid;
    public $mydb;
    public $rname;
    public $prefix;
    private $_prefix;
    private $_tableid;

    /*
     * 评论类
     */
    public function __construct() {
        parent::__construct();
    }

    // 设置空间操作评论
    public function space($name = '') {
        $this->mid = 'uid';
        $this->mydb = $this->db;
        $this->prefix = $this->db->dbprefix.'space';
        $this->rname = $name ? $name : $this->rname;
        $this->rname = $this->rname ? $this->rname : $this->ci->rname;
    }

    // 设置空间模型操作评论
    public function model($mid, $name = '') {
        $this->mid = 'id';
        $this->mydb = $this->db;
        $this->prefix = $this->db->dbprefix.'space_';
        $this->rname = $name ? $name : $this->rname;
        $this->rname = $this->rname ? $this->rname : $this->ci->rname;
    }

    // 设置模块操作评论
    public function module($dir, $name = '') {
        $this->mid = 'id';
        $this->mydb = $this->db;
        $this->prefix = $this->db->dbprefix.SITE_ID.'_'.$dir;
        $this->_prefix = $this->db->dbprefix.'{site}_'.$dir;
        $this->rname = $name ? $name : $this->rname;
        $this->rname = $this->rname ? $this->rname : $this->ci->rname;
    }

    // 设置模块扩展操作评论
    public function extend($dir, $name = '') {
        $this->mid = 'id';
        $this->mydb = $this->db;
        $this->prefix = $this->db->dbprefix.SITE_ID.'_'.$dir.'_extend';
        $this->_prefix = $this->db->dbprefix.'{site}_'.$dir.'_extend';
        $this->rname = $name ? $name : $this->rname;
        $this->rname = $this->rname ? $this->rname : $this->ci->rname;
    }

    // 卸载评论模块
    public function uninstall_sql() {

        $this->mydb->query("DROP TABLE IF EXISTS `{$this->prefix}_comment_my`");
        $this->mydb->query("DROP TABLE IF EXISTS `{$this->prefix}_comment_index`");
        for ($i = 0; $i < 100; $i ++) {
            if (!$this->mydb->query("SHOW TABLES LIKE '".$this->prefix.'_comment_data_'.$i."'")->row_array()) {
                break;
            }
            $this->mydb->query('DROP TABLE IF EXISTS '.$this->prefix.'_comment_data_'.$i);
        }

    }

    // 安装评论模块
    public function install_sql($siteid = 0) {

        if ($siteid) {
            // 从站点现存表中获取表结构
            $this->mydb = $this->site[$siteid];
            $this->prefix = str_replace('{site}', $siteid, $this->_prefix);
            if ($this->mydb->query("SHOW TABLES LIKE '".$this->prefix."_comment'")->row_array()) {
                $sql = $this->mydb->query("SHOW CREATE TABLE `".$this->prefix."_comment_my`")->row_array();
                if ($sql) {
                    $sql = str_replace(
                        array($sql['Table'], 'CREATE TABLE'),
                        array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                        $sql['Create Table']
                    );
                    $this->mydb->query($sql);
                    for ($i = 0; $i < 100; $i ++) {
                        if (!$this->mydb->query("SHOW TABLES LIKE '".$this->prefix.'_comment_data_'.$i."'")->row_array()) {
                            break;
                        }
                        $sql = $this->mydb->query("SHOW CREATE TABLE `".$this->prefix."_comment_data_".$i."`")->row_array();
                        $sql = str_replace(
                            array($sql['Table'], 'CREATE TABLE'),
                            array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                            $sql['Create Table']
                        );
                        $this->mydb->query($sql);
                    }
                    return;
                }
            }
        }

        $this->mydb->query(trim("
			CREATE TABLE IF NOT EXISTS `{$this->prefix}_comment_my` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid',
			  `title` varchar(250) DEFAULT NULL COMMENT '内容标题',
			  `url` varchar(250) DEFAULT NULL COMMENT 'URL地址',
			  `comments` int(10) unsigned DEFAULT '0' COMMENT '评论数量',
			  PRIMARY KEY (`id`),
			  KEY `cid` (`cid`),
			  KEY `uid` (`uid`),
			  KEY `comments` (`comments`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '我的评论表';
		"));

        $this->mydb->query(trim("
			CREATE TABLE IF NOT EXISTS `{$this->prefix}_comment_index` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
			  `support` int(10) unsigned DEFAULT '0' COMMENT '支持数',
			  `oppose` int(10) unsigned DEFAULT '0' COMMENT '反对数',
			  `comments` int(10) unsigned DEFAULT '0' COMMENT '评论数',
              `avgsort` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '平均分',
              `sort1` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort2` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort3` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort4` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort5` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort6` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort7` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort8` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
              `sort9` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
			  `tableid` smallint(5) unsigned DEFAULT '0' COMMENT '附表id',
			  PRIMARY KEY (`id`),
			  KEY `cid` (`cid`),
			  KEY `support` (`support`),
			  KEY `oppose` (`oppose`),
			  KEY `comments` (`comments`),
			  KEY `avgsort` (`avgsort`),
			  KEY `tableid` (`tableid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '评论索引表';
		"));

        $this->mydb->query(trim("
			CREATE TABLE IF NOT EXISTS `{$this->prefix}_comment_data_0` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论ID',
			  `cid` int(10) unsigned NOT NULL COMMENT '关联id',
			  `uid` mediumint(8) unsigned DEFAULT '0' COMMENT '会员ID',
			  `url` varchar(250) DEFAULT NULL COMMENT '主题地址',
			  `title` varchar(250) DEFAULT NULL COMMENT '主题名称',
			  `author` varchar(250) DEFAULT NULL COMMENT '评论者',
			  `content` text COMMENT '评论内容',
			  `support` int(10) unsigned DEFAULT '0' COMMENT '支持数',
			  `oppose` int(10) unsigned DEFAULT '0' COMMENT '反对数',
              `avgsort` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '平均分',
			  `sort1` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort2` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort3` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort4` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort5` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort6` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort7` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort8` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `sort9` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '评分值',
			  `reply` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复id',
			  `in_reply` TINYINT(1) UNSIGNED DEFAULT '0' COMMENT '是否存在回复',
			  `status` smallint(1) unsigned DEFAULT '0' COMMENT '审核状态',
			  `inputip` varchar(50) DEFAULT NULL COMMENT '录入者ip',
			  `inputtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '录入时间',
			  PRIMARY KEY (`id`),
			  KEY `uid` (`uid`),
			  KEY `cid` (`cid`),
			  KEY `reply` (`reply`),
			  KEY `support` (`support`),
			  KEY `oppose` (`oppose`),
			  KEY `avgsort` (`avgsort`),
			  KEY `status` (`status`),
			  KEY `aa` (`cid`,`status`,`inputtime`),
			  KEY `inputtime` (`inputtime`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '评论内容表';
		"));

    }

    // 获取评论总数
    public function get_total($cid) {
        $data = $this->mydb->where('cid', $cid)->get($this->prefix.'_comment_index')->row_array();
        return (int)$data['comments'];
    }

    // 获取评论数
    public function total_info($cid) {
        return $this->mydb->where('cid', $cid)->get($this->prefix.'_comment_index')->row_array();
    }

    // 评论索引获取表名称
    public function get_table($cid, $is_all = 0) {

        // 评论索引不存在时就创建新的评论索引记录
        $row = $this->mydb->where('cid', $cid)->get($this->prefix.'_comment_index')->row_array();
        if (!$row) {
            // 插入索引表数据
            $this->mydb->insert($this->prefix.'_comment_index', array(
                'cid' => $cid,
                'sort1' => 0,
                'sort2' => 0,
                'sort3' => 0,
                'sort4' => 0,
                'sort5' => 0,
                'sort6' => 0,
                'sort7' => 0,
                'sort8' => 0,
                'sort9' => 0,
                'oppose' => 0,
                'support' => 0,
                'tableid' => 0,
                'avgsort' => 0,
                'comments' => 0,
            ));
            $id = $this->mydb->insert_id();
            if ($this->cconfig['value']['fenbiao']['use']) {
                // 以5w左右数据量无限分表
                $tableid = floor($id/50000);
                if (!$this->db->query("SHOW TABLES LIKE '%".$this->prefix.'_comment_data_'.$tableid."%'")->row_array()) {
                    // 附表不存在时创建附表
                    $sql = $this->db->query("SHOW CREATE TABLE `{$this->prefix}_comment_data_0`")->row_array();
                    $this->db->query(str_replace($sql['Table'], $this->prefix.'_comment_data_'.$tableid, $sql['Create Table']));
                }
            } else {
                $tableid = 0;
            }
        } else {
            $tableid = (int)$row['tableid'];
        }

        $this->_tableid = $tableid;

        return $is_all ? array($this->prefix.'_comment_data_'.$tableid, $row) : $this->prefix.'_comment_data_'.$tableid;
    }

    // 获取主数据
    public function get_cdata($cid) {

        if (!$cid) {
            return;
        }

        list($a, $name, $dir) = explode('-', $this->rname);

        switch ($name) {
            case 'module':
                // 模块评论
                $data = $this->mydb->where('id', $cid)->get($this->prefix)->row_array();
                if (!$data) {
                    return;
                }
                break;
            case 'extend':
                // 扩展评论
                $data = $this->mydb->where('id', $cid)->get($this->prefix)->row_array();
                if (!$data) {
                    return;
                }
                $data['title'] = $data['name'];
                break;
            case 'space':
                $data = $this->mydb->where('uid', $cid)->get($this->prefix)->row_array();
                if (!$data) {
                    return;
                }
                $data['id'] = $data['id'];
                $data['url'] = dr_space_url($cid);
                $data['title'] = $data['name'];
                break;
            case 'model':
                break;
        }

        return $data;
    }


    // 需要审核的评论
    public function verify($table, $id) {

        if (!$table || !$id) {
            return;
        }

        $row = $this->mydb->where('id', $id)->get($table)->row_array();
        $cid = (int)$row['cid'];
        $uid = (int)$row['uid'];
        $data = $this->get_cdata($cid);;
        if (!$row || !$data || $row['status']) {
            return;
        }

        // 变更审核状态
        $this->mydb->where('id', $id)->update($table, array('status' => 1));

        if ($row['reply']) {
            $this->member_model->add_notice($row['uid'], 2, fc_lang('您的评论被人回复，<a href="%s" target="_blank">查看详情</a>', $data['url'].'#comment-'.$id));
        } else {
            $this->member_model->add_notice($data['uid'], 2, fc_lang('您有新的评论，<a href="%s" target="_blank">查看详情</a>', $data['url'].'#comment-'.$id));
        }

        $markrule = $this->member_model->get_markrule($row['uid']);
        if ($markrule && $row['uid']) {
            $permission = $this->cconfig['value']['permission'][$markrule];
            // 增加经验值
            $permission['experience'] && $this->member_model->update_score(0, $uid, abs($permission['experience']), '', '评论');
            // 增加虚拟币}
            $permission['score'] && $this->member_model->update_score(1, $uid, $permission['score'], '', '评论');
            // 我的评论
            $my = $this->mydb->where('cid', $cid)->where('uid', $uid)->get($this->prefix.'_comment_my')->row_array();
            if ($my) {
                // 更新评论数据
                $this->mydb->where('id', $my['id'])->update($this->prefix.'_comment_my', array(
                    'url' => $data['url'],
                    'title' => $data['title'],
                    'comments' => (int)$my['comments'] + 1
                ));
            } else {
                $this->mydb->insert($this->prefix.'_comment_my', array(
                    'cid' => $cid,
                    'uid' => $uid,
                    'url' => $data['url'],
                    'title' => $data['title'],
                    'comments' => 1
                ));
            }
        }

        // 更新数量
        $this->mydb->where($this->mid, $cid)->set('comments', 'comments+1', false)->update($this->prefix);
        $this->mydb->where('cid', $cid)->set('comments', 'comments+1', false)->update($this->prefix.'_comment_index');

        // 回复评论时，将主题设置为存在回复状态
        $row['reply'] && $this->mydb->where('id', $row['reply'])->update($table, array(
            'in_reply' => 1,
        ));

        // 更新点评数据
        $this->update_review($table, $row);

        // 调用发布后执行的动作函数
        $this->ci->_post_commnet(array_merge($data, $row));
    }

    // 发布评论
    public function post($uid, $data, $my = array()) {

        $cid = (int)$data['cid'];
        if (!$cid) {
            return 0;
        }
        $rid = (int)$data['rid'];
        $table = $this->get_table($cid);
        $m = $this->uid == $uid ? $this->member : dr_member_info($uid);

        if ($rid && $row = $this->mydb->where('id', $rid)->get($table)->row_array()) {
            $row['reply'] && $rid = $row['reply'];
            // 提醒被回复者
            !$data['verify'] && $this->member_model->add_notice($row['uid'], 2, fc_lang('您的评论被人回复，<a href="%s" target="_blank">查看详情</a>', $data['url']));
        } else {
            // 提醒作者被评论
            !$data['verify'] && $this->member_model->add_notice($data['uid'], 2, fc_lang('您有新的评论，<a href="%s" target="_blank">查看详情</a>', $data['url']));
        }

        $insert = array();
        $insert['cid'] = $cid;
        $insert['url'] = $data['url'];
        $insert['title'] = $data['title'];
        $insert['uid'] = $uid;
        $insert['reply'] = $rid;
        $insert['status'] = $data['verify'] ? 0 : 1;
        $insert['author'] = $m ? $m['username'] : '游客';
        $insert['content'] = $data['content'];
        $insert['support'] = $insert['oppose'] = $insert['avgsort'] = 0;
        $insert['inputip'] = $this->input->ip_address();
        $insert['inputtime'] = SYS_TIME;
        // 点评选项值
        for ($i = 1; $i <= 9; $i++) {
            $insert['sort'.$i] = isset($data['review'][$i]) ? (int)$data['review'][$i] : 0;
        }

        // 自定义字段入库
        isset($my[1]) && count($my[1]) && $insert = array_merge($insert, $my[1]);

        // 数据插入评论表
        $this->mydb->insert($table, $insert);
        $insert['id'] = $rid = $this->mydb->insert_id();

        // 需要审核时直接返回
        if (!$insert['status']) {
            $this->member_model->admin_notice('content', '新评论审核', $this->uri.'show/tid/'.$this->_tableid.'/id/'.$insert['id']);
            return $rid;
        }

        // 回复评论时，将主题设置为存在回复状态
        $insert['reply'] && $this->mydb->where('id', $insert['reply'])->update($table, array(
            'in_reply' => 1,
        ));

        // 我的评论
        if ($this->uid) {
            $my = $this->mydb->where('cid', $cid)->where('uid', $uid)->get($this->prefix.'_comment_my')->row_array();
            if ($my) {
                // 更新评论数据
                $this->mydb->where('id', $my['id'])->update($this->prefix.'_comment_my', array(
                    'url' => $data['url'],
                    'title' => $data['title'],
                    'comments' => (int)$my['comments'] + 1
                ));
            } else {
                $this->mydb->insert($this->prefix.'_comment_my', array(
                    'cid' => $cid,
                    'uid' => $uid,
                    'url' => $data['url'],
                    'title' => $data['title'],
                    'comments' => 1
                ));
            }
        }

        // 更新数量
        $this->mydb->where($this->mid, $cid)->set('comments', 'comments+1', false)->update($this->prefix);
        $this->mydb->where('cid', $cid)->set('comments', 'comments+1', false)->update($this->prefix.'_comment_index');

        // 更新点评数据
        $data['review'] && $this->update_review($table, $insert);

        return $rid;
    }

    // 删除评论
    public function delete($rid, $cid, $index = array()) {

        if (!$rid) {
            return;
        }

        if (!$index) {
            $index = $this->mydb->where('cid', $cid)->get($this->prefix.'_comment_index')->row_array();
            if (!$index) {
                return;
            }
        }

        $table = $this->prefix.'_comment_data_'.intval($index['tableid']);
        $data = $this->mydb->where('id', $rid)->get($table)->row_array();
        if (!$data) {
            return;
        }

        // 删除评论数据
        $this->mydb->where('id', $rid)->delete($table);
        // 删除表对应的附件
        $this->load->model('attachment_model');
        $this->attachment_model->delete_for_table($table.'-'.$rid);
        $this->mydb->where('reply', $rid)->delete($table);

        // 统计评论总数
        $comments = $this->mydb->where('cid', $cid)->where('status', 1)->count_all_results($table);
        $this->mydb->where($this->mid, $index['id'])->update($this->prefix.'_comment_index', array(
            'comments' => $comments
        ));

        // 更新我的评论
        if ($data['uid']) {
            $my = $this->mydb->where('cid', $cid)->where('uid', $data['uid'])->get($this->prefix.'_comment_my')->row_array();
            if ($my) {
                // 更新评论数据
                $comments = $this->mydb->where('cid', $cid)->where('uid', $data['uid'])->where('status', 1)->count_all_results($table);
                $this->mydb->where('id', $my['id'])->update($this->prefix.'_comment_my', array(
                    'comments' => $comments
                ));
            }
        }

        // 更新点评分数
        $this->update_review($table, $data);
    }

    // 更新点评数据
    public function update_review($table, $data) {

        $review = $set = array();
        $_avgsort = 0;
        for ($i = 1; $i <= 9; $i++) {
            if ($data['sort'.$i]) {
                $review[$i] = $data['sort'.$i];
                $set['sort'.$i] = 0;
                $_avgsort += $review[$i];
            }
        }

        // 分值不存在时不更新
        if (!$review) {
            return ;
        }

        // 统计总的点评数
        $this->mydb->where('cid', (int)$data['cid']);
        $this->mydb->select_sum('status', 'num');
        foreach($review as $i => $val) {
            $this->mydb->select_sum('sort'.$i);
        }
        $grade = $this->mydb->where('status', 1)->where('reply', 0)->get($table)->row_array();

        if (!$grade) {
            return;
        }

        // 算法类别
        $st = round(max((int)$this->cconfig['value']['review']['score'], 5) / 5); //显示分数制 5分，10分，百分
        $dl = empty($this->cconfig['value']['review']['point']) || $this->cconfig['value']['review']['point'] < 0 ? 0 : $this->cconfig['value']['review']['point']; //小数点位数

        // 分别计算各个选项分数
        foreach($review as $i => $aaaaa) {
            $flag = 'sort'.$i;
            $set[$flag] = $grade[$flag] ? round( ($grade[$flag] / $grade['num'] * $st), $dl) : 0;
            $set['avgsort']+= $set[$flag];
        }
        $set['avgsort'] = round(($set['avgsort'] / count($review)), $dl);

        // 更新到主表
        $this->mydb->where($this->mid, (int)$data['cid'])->update($this->prefix, array(
            'avgsort' => $set['avgsort'],
        ));
        // 更新到索引表
        $this->mydb->where('cid', (int)$data['cid'])->update($this->prefix.'_comment_index', $set);

        // 更新当前记录的平均分
        $this->mydb->where('id', (int)$data['id'])->update($table, array(
            'avgsort' => round(($_avgsort / count($review)), $dl),
        ));
    }
}