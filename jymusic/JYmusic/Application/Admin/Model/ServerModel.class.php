<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Model;
use Think\Model;


class ServerModel extends Model {
		protected $_map = array(               
		'surl'  	=>'url', 
	);
	
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        array('url', 'require', '地址不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', 1, self::MODEL_INSERT),
        array('listen_dir', 'checklisten', self::MODEL_BOTH,'callback'),
        array('down_dir', 'checkdown', self::MODEL_BOTH,'callback')
    );
    
    
    function checklisten () {
    	$dir= trim(I('post.listen_dir'));
    	if(!empty($dir) && substr($dir,-1) != '/'){
			$dir = $dir.'/';
		}	
    	return $dir;
    }
    
    function checkdown () {
    	$dir= trim(I('post.down_dir'));   	
    	if(!empty($dir) && substr($dir,-1) != '/'){
			$dir = $dir.'/';
		}	
    	return $dir;
    }

}
