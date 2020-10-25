<?php
namespace Action;
use HY\Action;
!defined('HY_PATH') && exit('HY_PATH not defined.');
use PDO;

class Inst extends Action {
    public $state;
    // public function index(){
    //     if(C('DOMAIN_NAME')){
    //       header("location: " . C('DOMAIN_NAME'));
    //       exit;
    //     }
    //     //$this->view = 'install';
    //     //$this->display('index');
    // }
    private function app_text($str){
      $this->state.='<p><i class="fa fa-check"></i> '.$str.'</p>';
    }
    
    public function install(){
        die('install');
    }
    public function index(){
        // if(C('DOMAIN_NAME')){
        //   header("location: " . C('DOMAIN_NAME'));
        //   exit;
        // }
        
        $DOMAIN_NAME = C('DOMAIN_NAME');
       


        if(!empty($DOMAIN_NAME)){
          if(IS_AJAX)
          $this->json(array('error'=>false,'info'=>'你已经安装过,如果需要重装请将 /Conf/config.php删除'));
            else
          die('你已经安装过,如果需要重装请将 /Conf/config.php删除');
        }
        $bbs_user = X('post.bbs_user');
        $bbs_pass = X('post.bbs_pass');
        $email = X('post.email');
        $www = X('post.www');
        !empty($bbs_user) or $this->json(array('error'=>false,'info'=>'请输入管理员用户名'));
        !empty($bbs_pass) or $this->json(array('error'=>false,'info'=>'请输入管理员密码 (最少6位)'));
        !empty($email) or $this->json(array('error'=>false,'info'=>'请输入管理员邮箱'));
        !empty($www) or $this->json(array('error'=>false,'info'=>'请输入网站域名'));


        
        $sql = new \HY\Lib\Medoo(array(
            // 必须配置项
            'database_type' => X("post.sqltype"),
            'database_name' => X("post.name"),
            'server' => X("post.ip"),
            'username' => X("post.username"),
            'password' => X("post.password"),
            'charset' => 'utf8',
            // 可选参数
            'port' => X("post.port"),
            // 可选，定义表的前缀
            'prefix' => 'hy_',
        ));

        

        $table_type = X("post.table_type");

        $content = @file_get_contents(INDEX_PATH . 'Conf/config.back');
        if($content === false)
          $this->json(array('error'=>false,'info'=>'/Conf无读取权限'));
        $str = rand_str(16);
        $content = str_replace(

          array(
            'MYSQL_NAME',
            'MYSQL_IP',
            'MYSQL_USER',
            'MYSQL_PASS',
            'MYSQL_PORT',
            'http://127.0.0.1',
            'sql_typee',
            '1234567890',
            'SQL_STORAGE_ENGINE_VALUE'
          ),
          array(
            X("post.name"),
            X("post.ip"),
            X("post.username"),
            X("post.password"),
            X("post.port"),
            (X("post.https")=='on'?'https://':'http://').trim(X("post.www"),'/'),
            X("post.sqltype"),
            $str,
            $table_type

          ),$content
        );

        $salt = substr(md5(mt_rand(10000000, 99999999).NOW_TIME), 0, 8);

        if(@file_put_contents(INDEX_PATH . 'Conf/config.php',$content) === false)
          $this->json(array('error'=>false,'info'=>'/Conf无写入权限'));



if($sql->exec("
DROP TABLE IF EXISTS hy_count;
DROP TABLE IF EXISTS hy_forum;
DROP TABLE IF EXISTS hy_post;
DROP TABLE IF EXISTS hy_thread;
DROP TABLE IF EXISTS hy_user;
DROP TABLE IF EXISTS hy_usergroup;
DROP TABLE IF EXISTS hy_vote;
DROP TABLE IF EXISTS hy_file;
DROP TABLE IF EXISTS hy_fileinfo;
DROP TABLE IF EXISTS hy_filegold;
DROP TABLE IF EXISTS hy_threadgold;
DROP TABLE IF EXISTS hy_cache;
DROP TABLE IF EXISTS hy_ol;
DROP TABLE IF EXISTS hy_vote_post;
DROP TABLE IF EXISTS hy_vote_thread;
DROP TABLE IF EXISTS hy_chat;
DROP TABLE IF EXISTS hy_chat_count;
DROP TABLE IF EXISTS hy_friend;
DROP TABLE IF EXISTS hy_forum_group;
DROP TABLE IF EXISTS hy_log;

CREATE TABLE if not exists `hy_chat` (
  `uid1` int(10) NOT NULL,
  `uid2` int(10) NOT NULL,
  `content` tinytext NOT NULL,
  `atime` int(10) NOT NULL,
  KEY `uid1` (`uid1`,`uid2`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_chat_count` (
  `uid` int(10) NOT NULL,
  `c` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `atime` int(10) NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_friend` (
  `uid1` int(10) NOT NULL,
  `uid2` int(10) NOT NULL,
  `c` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `atime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  KEY `uid1` (`uid1`),
  KEY `uid2` (`uid2`),
  UNIQUE KEY `uid1_uid2` (`uid1`, `uid2`),
  KEY `uid1_uid2_state` (`uid1`, `uid2`, `state`),
  KEY `uid1_state` (`uid1`,`state`),
  KEY `uid2_state` (`uid2`,`state`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_cache` (
  `cachekey` varchar(255) NOT NULL,
  `expire` int(10) NOT NULL,
  `data` blob,
  `datacrc` int(32) DEFAULT NULL,
  UNIQUE KEY `cachekey` (`cachekey`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_vote_post` (
  `uid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `atime` int(10) NOT NULL,
  KEY `uid` (`uid`),
  KEY `pid` (`pid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_vote_thread` (
  `uid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `atime` int(10) NOT NULL,
  KEY `uid` (`uid`),
  KEY `tid` (`tid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_ol` (
  `uid` int(10) NOT NULL,
  `username` varchar(18) NOT NULL,
  `ip` int(10) NOT NULL,
  `group` tinyint(3) NOT NULL,
  `atime` int(10) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `atime` (`atime`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_count` (
  `name` varchar(12) NOT NULL,
  `v` int(10) NOT NULL DEFAULT '0',
  UNIQUE KEY `name` (`name`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
INSERT INTO `hy_count` (`name`, `v`) VALUES
('A1.0', 1),
('A1.1', 1),
('A1.2', 1),
('1.5', 1),
('1.5.1', 1),
('1.5.27', 1),
('thread', 0);
CREATE TABLE if not exists `hy_file` ( 
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '附件ID' ,
  `uid` INT NOT NULL COMMENT '附件主人UID' ,
  `filename` TEXT NOT NULL COMMENT '附件名称' ,
  `md5name` TEXT NOT NULL COMMENT '附件随机名' ,
  `filesize` INT UNSIGNED NOT NULL COMMENT '文件大小' ,
  `atime` INT UNSIGNED NOT NULL COMMENT '添加时间' ,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE if not exists `hy_fileinfo` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '帖子附件ID' ,
  `fileid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `gold` int(10) NOT NULL,
  `hide` TINYINT(1) NOT NULL,
  `downs` int(10) NOT NULL,
  `mess` text NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `fileid` (`fileid`) USING BTREE,
  KEY `tid` (`tid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE={$table_type} DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE if not exists `hy_forum` (
  `id` int(10) NOT NULL,
  `fid` int(10) NOT NULL DEFAULT '-1',
  `fgid` INT NOT NULL DEFAULT '1',
  `name` varchar(12) NOT NULL,
  `name2` varchar(18) NOT NULL,
  `threads` int(10) NOT NULL DEFAULT '0',
  `posts` int(10) NOT NULL DEFAULT '0',
  `forumg` text NOT NULL,
  `json` text NOT NULL,
  `html` longtext NOT NULL,
  `color` varchar(30) NOT NULL,
  `background` varchar(30) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `fid` (`fid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
INSERT INTO `hy_forum` (`id`, `fid`, `name`,`name2`, `threads`) VALUES
(0, -1, '默认分类','morenfenlei', 0),
(1, -1, '分类1','fenlei1', 0),
(2, -1, '分类2','fenlei2', 0),
(3, -1, '分类3','fenlei3', 0);

CREATE TABLE if not exists `hy_forum_group` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
INSERT INTO `hy_forum_group` (`id`, `name`) VALUES
  (1, '默认分组');
CREATE TABLE if not exists `hy_filegold` ( 
  `uid` INT NOT NULL , 
  `fileinfoid` INT NOT NULL , 
   KEY `uid` (`uid`),
   KEY `fileinfoid` (`fileinfoid`),
   KEY `uid_fileinfoid` (`uid`, `fileinfoid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8 ;
CREATE TABLE if not exists `hy_post` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tid` int(10) NOT NULL,
  `fid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `isthread` tinyint(1) NOT NULL DEFAULT '0',
  `content` longtext NOT NULL,
  `atime` int(10) NOT NULL,
  `goods` int(10) DEFAULT '0',
  `nos` int(10) NOT NULL DEFAULT '0',
  `posts` int(10) NOT NULL DEFAULT '0',
   UNIQUE KEY `id` (`id`),
   KEY `tid` (`tid`),
   KEY `uid` (`uid`),
   KEY `uid_isthread` (`uid`, `isthread`),
   KEY `atime` (`atime`),
   KEY `tid_isthread` (`tid`, `isthread`),
   KEY `tid_uid` (`tid`, `uid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE if not exists `hy_thread` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fid` int(10) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT 'user_id',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `title` char(128) NOT NULL,
  `summary` text NOT NULL,
  `atime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `btime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `buid`  int(10) NOT NULL DEFAULT '0',
  `views` int(10) NOT NULL DEFAULT '0' COMMENT 'view_size',
  `posts` int(10) NOT NULL DEFAULT '0' COMMENT 'post_size',
  `goods` int(10) NOT NULL DEFAULT '0',
  `nos` int(10) NOT NULL DEFAULT '0',
  `img` text NOT NULL,
  `img_count` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `top` tinyint(1) NOT NULL DEFAULT '0',
  `files` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '附件数量',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `gold` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
   UNIQUE KEY `id` (`id`),
   KEY `uid` (`uid`),
   KEY `fid` (`fid`),
   KEY `top` (`top`),
   KEY `btime` (`btime`),
   KEY `top_fid` (`top`, `fid`),
   KEY `img_count` (`img_count`),
   KEY `atime` (`atime`),
   KEY `posts` (`posts`),
   KEY `views` (`views`),
   KEY `goods` (`goods`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE if not exists `hy_threadgold` (
  `uid` int(10) UNSIGNED NOT NULL,
  `tid` int(10) UNSIGNED NOT NULL,
  UNIQUE KEY `tid_uid` (`tid`, `uid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE if not exists `hy_log` ( 
  `uid` INT UNSIGNED NOT NULL , 
  `gold` INT NOT NULL , 
  `credits` INT NOT NULL , 
  `content` VARCHAR(32) NOT NULL , 
  `atime` INT UNSIGNED NOT NULL , 
  KEY `uid` (`uid`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `hy_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user` varchar(18) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `threads` int(10) UNSIGNED NOT NULL,
  `posts` int(10) UNSIGNED NOT NULL,
  `atime` int(10) UNSIGNED NOT NULL,
  `group` smallint(2) NOT NULL DEFAULT '0',
  `gold` int(10) NOT NULL DEFAULT '0' COMMENT '金钱',
  `credits` int(10) NOT NULL DEFAULT '0',
  `mess` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `etime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ps` varchar(40) DEFAULT NULL,
  `fans` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `follow` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ctime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `chat_size` int(10) UNSIGNED NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `user` (`user`),
  KEY `email` (`email`),
  KEY `atime` (`atime`),
  KEY `group` (`group`)
) ENGINE={$table_type}  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
INSERT INTO `hy_user` (`id`, `user`, `pass`, `email`, `salt`, `threads`, `posts`, `atime`, `group`) VALUES
(1, '".X("post.bbs_user")."', '".L("User")->md5_md5(X("post.bbs_pass"),$salt)."', '".X("post.email")."', '".$salt."', 0, 0, ".NOW_TIME.", 1);
CREATE TABLE if not exists `hy_usergroup` (
  `id` int(10) NOT NULL,
  `credits` int(10) NOT NULL DEFAULT '-1',
  `space_size` int(10) UNSIGNED DEFAULT '4294967295',
  `chat_size` int(10) UNSIGNED DEFAULT '4294967295',
  `name` varchar(12) NOT NULL,
  `json` varchar(120) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE={$table_type} DEFAULT CHARSET=utf8;
INSERT INTO `hy_usergroup` (`id`, `space_size`, `chat_size`, `name`, `json`) VALUES
(1, 4294967295, 4294967295, '管理员', '{\"uploadfile\":1,\"down\":1,\"del\":1,\"upload\":1,\"mess\":1,\"post\":1,\"thread\":1,\"tgold\":1,\"thide\":1,\"nogold\":0}'),
(2, 4294967295, 4294967295, '新用户', '{\"down\":1,\"uploadfile\":1,\"del\":1,\"upload\":1,\"mess\":1,\"post\":1,\"thread\":1,\"nogold\":0,\"thide\":1,\"tgold\":1}'),
(3, 4294967295, 4294967295, '游客', '{\"down\":1,\"uploadfile\":1,\"del\":1,\"upload\":1,\"mess\":1,\"post\":1,\"thread\":1,\"nogold\":0,\"thide\":1,\"tgold\":1}');


") )
  $this->json(array('error'=>false,'info'=>'创建SQL失败'));

$this->app_text('Insert Data success');




      

      //if(is_file(ACTION_PATH . 'Install.php'))
          //rename(ACTION_PATH . 'Install.php' , ACTION_PATH . 'Install.php.back');
      
      $this->json(array('error'=>true,'info'=>$this->state,'url'=>(X("post.https")=='on'?'https://':'http://').trim(X("post.www"),'/') ));
      


        //echo X("post.name");
    }

}
