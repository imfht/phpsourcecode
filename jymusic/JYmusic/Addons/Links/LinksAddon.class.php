<?php

namespace Addons\Links;
use Common\Controller\Addon;
use Think\Db;
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+

 class LinksAddon extends Addon{

        public $info = array(
            'name'=>'Links',
            'title'=>'友情连接',
            'description'=>'友情连接插件',
            'status'=>1,
            'author'=>'JYmusic',
            'version'=>'0.1'
        );
        
        public $addon_path = './Addons/Links/';
        
        /**
         * 配置列表页面
         * @var unknown_type
         */
        public $admin_list = array(
        		'listKey' => array(
        				'title'=>'站点名称',
        				'typetext'=>'类型',
        				'statustext'=>'显示状态',
        				'level'=>'优先级',
        				'create_time'=>'开始时间',
        		),
        		'model'=>'Links',
        		'order'=>'level desc,id asc'
        );
        public $custom_adminlist = 'adminlist.html';
 
        /**
         * (non-PHPdoc)
         * 安装函数
         * @see \Common\Controller\Addons::install()
         */
        public function install(){
        	$db_config = array();
        	$db_config['DB_TYPE'] = C('DB_TYPE');
        	$db_config['DB_HOST'] = C('DB_HOST');
        	$db_config['DB_NAME'] = C('DB_NAME');
        	$db_config['DB_USER'] = C('DB_USER');
        	$db_config['DB_PWD'] = C('DB_PWD');
        	$db_config['DB_PORT'] = C('DB_PORT');
        	$db_config['DB_PREFIX'] = C('DB_PREFIX');
        	$db = Db::getInstance($db_config);
        	//读取插件sql文件
        	$sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/install.sql');
        	$sqlFormat = $this->sql_split($sqldata, $db_config['DB_PREFIX']);
        	$counts = count($sqlFormat);
        	
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);

                if (strstr($sql, 'CREATE TABLE')) {
                    preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                    mysql_query("DROP TABLE IF EXISTS `$matches[1]");
                    $db->execute($sql);
                }
            }
            return true;
        }

        /**
         * (non-PHPdoc)
         * 卸载函数
         * @see \Common\Controller\Addons::uninstall()
         */
        public function uninstall(){
        	$db_config = array();
        	$db_config['DB_TYPE'] = C('DB_TYPE');
        	$db_config['DB_HOST'] = C('DB_HOST');
        	$db_config['DB_NAME'] = C('DB_NAME');
        	$db_config['DB_USER'] = C('DB_USER');
        	$db_config['DB_PWD'] = C('DB_PWD');
        	$db_config['DB_PORT'] = C('DB_PORT');
        	$db_config['DB_PREFIX'] = C('DB_PREFIX');
        	$db = Db::getInstance($db_config);
        	//读取插件sql文件
        	$sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/uninstall.sql');
        	$sqlFormat = $this->sql_split($sqldata, $db_config['DB_PREFIX']);
        	$counts = count($sqlFormat);
        	 
        	for ($i = 0; $i < $counts; $i++) {
        		$sql = trim($sqlFormat[$i]);
        		$db->execute($sql);//执行语句
        	}
            return true;
        }      
        
         //实现的pageFooter底部钩子
        public function pageFooter($param){
			//dump($param);
        	if($param['widget'] == 'link'){
        		$list = D('Addons://Links/Links')->linkList();
        		$config = $this->getConfig();
				$this->assign('addons_config', $config);
				$this->assign('list',$list);
				$this->assign('link',$param);
				$this->display('widget');
        	}
        }
        
        /**      
         * 解析数据库语句函数
         * @param string $sql  sql语句   带默认前缀的
         * @param string $tablepre  自己的前缀
         * @return multitype:string 返回最终需要的sql语句
         */
        public function sql_split($sql, $tablepre) {
        
        	if ($tablepre != "onethink_")
        		$sql = str_replace("onethink_", $tablepre, $sql);
        	$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        
        	if ($r_tablepre != $s_tablepre)
        		$sql = str_replace($s_tablepre, $r_tablepre, $sql);
        	$sql = str_replace("\r", "\n", $sql);
        	$ret = array();
        	$num = 0;
        	$queriesarray = explode(";\n", trim($sql));
        	unset($sql);
        	foreach ($queriesarray as $query) {
        		$ret[$num] = '';
        		$queries = explode("\n", trim($query));
        		$queries = array_filter($queries);
        		foreach ($queries as $query) {
        			$str1 = substr($query, 0, 1);
        			if ($str1 != '#' && $str1 != '-')
        				$ret[$num] .= $query;
        		}
        		$num++;
        	}
        	return $ret;
        }        
        
    }