<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/* 解析列表定义规则*/

function get_list_field($data, $grid){

    // 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =	$data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

    // 链接支持
    if(!empty($grid['href'])){
        $links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href   =   str_replace(
                    array('[DELETE]','[EDIT]','[LIST]'),
                    array('del?ids=[id]&model=[model_id]',
                    'edit?id=[id]&model=[model_id]&cate_id=[category_id]',
                    'index?pid=[id]&model=[model_id]&cate_id=[category_id]'),
                    $href);

                // 替换数据变量
                $href	=	preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]	=	'<a href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
    }
    return $value;
}

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/* 解析列表定义规则*/

function get_addonlist_field($data, $grid,$addon){
    // 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =    $data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

    // 链接支持
    if(!empty($grid['href'])){
        $links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href   =   str_replace(
                    array('[DELETE]','[EDIT]','[ADDON]'),
                    array('del?ids=[id]&name=[ADDON]','edit?id=[id]&name=[ADDON]',$addon),
                    $href);

                // 替换数据变量
                $href   =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]  =   '<a href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
    }
    return $value;
}

// 获取模型名称
function get_model_by_id($id){
    return $model = M('Model')->getFieldById($id,'title');
}

// 获取属性类型信息
function get_attribute_type($type=''){
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
        'string'    =>  array('字符串','varchar(255) NOT NULL'),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'date'      =>  array('日期','int(10) NOT NULL'),
        'datetime'  =>  array('时间','int(10) NOT NULL'),
        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
        'select'    =>  array('枚举','char(50) NOT NULL'),
        'radio'     =>  array('单选','char(10) NOT NULL'),
        'checkbox'  =>  array('多选','varchar(100) NOT NULL'),
        'editor'    =>  array('编辑器','text NOT NULL'),
        'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
        'file'      =>  array('上传附件','int(10) UNSIGNED NOT NULL'),
    );
    return $type?$_type[$type][0]:$_type;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null){
    if(!isset($status)){
        return false;
    }
    switch ($status){
    	case -2  : return    '未通过';   break;
        case -1 : return    '已删';   break;
        case 0  : return    '禁用';     break;
        case 1  : return    '正常';     break;
        case 2  : return    '待审';   break;        
        default : return    false;      break;
    }
}

// 获取数据的状态操作
function show_status_op($status) {
    switch ($status){
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';		break;
        default : return    false;      break;
    }
}

/**
 * 获取文档的类型文字
 * @param string $type
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_document_type($type = null){
    if(!isset($type)){
        return false;
    }
    switch ($type){
        case 1  : return    '目录'; break;
        case 2  : return    '主题'; break;
        case 3  : return    '段落'; break;
        default : return    false;  break;
    }
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    $list = C('CONFIG_GROUP_LIST');
    return $group?$list[$group]:'';
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
function extra_menu($extra_menu,&$base_menu){
    foreach ($extra_menu as $key=>$group){
        if( isset($base_menu['child'][$key]) ){
            $base_menu['child'][$key] = array_merge( $base_menu['child'][$key], $group);
        }else{
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 获取参数的所有父级分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category($cid){
    if(empty($cid)){
        return false;
    }
    $cates  =   M('Category')->where(array('status'=>1))->field('id,title,pid')->order('sort')->select();
    $child  =   get_category($cid);	//获取参数分类的信息
    $pid    =   $child['pid'];
    $temp   =   array();
    $res[]  =   $child;
    while(true){
        foreach ($cates as $key=>$cate){
            if($cate['id'] == $pid){
                $pid = $cate['pid'];
                array_unshift($res, $cate);	//将父分类插入到数组第一个元素前
            }
        }
        if($pid == 0){
            break;
        }
    }
    return $res;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 获取当前分类的文档类型
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_type_bycate($id = null){
    if(empty($id)){
        return false;
    }
    $type_list  =   C('DOCUMENT_MODEL_TYPE');
    $model_type =   M('Category')->getFieldById($id, 'type');
    $model_type =   explode(',', $model_type);
    foreach ($type_list as $key=>$value){
        if(!in_array($key, $model_type)){
            unset($type_list[$key]);
        }
    }
    return $type_list;
}

/**
 * 获取当前文档的分类
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null){
    if(empty($cate_id)){
        return false;
    }
    $cate   =   M('Category')->where('id='.$cate_id)->getField('title');
    return $cate;
}

 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

// 获取子文档数目
function get_subdocument_count($id=0){
    return  M('Document')->where('pid='.$id)->count();
}



 // 分析枚举类型字段值 格式 a:名称1,b:名称2
 // 暂时和 parse_config_attr功能相同
 // 但请不要互相使用，后期会调整
function parse_field_attr($string) {
    if(0 === strpos($string,':')){
        // 采用函数定义
        return   eval('return '.substr($string,1).';');
    }elseif(0 === strpos($string,'[')){
        // 支持读取配置参数（必须是数组类型）
        return C(substr($string,1,-1));
    }
    
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null){
	if(empty($id) && !is_numeric($id)){
		return false;
	}
	$list = S('action_list');
	if(empty($list[$id])){
		$map = array('status'=>array('gt', -1), 'id'=>$id);
		$list[$id] = M('Action')->where($map)->field(true)->find();
	}
	return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @author huajie <banhuajie@163.com>
 */
function get_document_field($value = null, $condition = 'id', $field = null){
	if(empty($value)){
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info = M('Model')->where($map);
	if(empty($field)){
		$info = $info->field(true)->find();
	}else{
		$info = $info->getField($field);
	}
	return $info;
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false){
	$list = array(
		1=>'系统',
		2=>'用户',
	);
	if($all){
		return $list;
	}
	return $list[$type];
}

/**
 * 根据表名获取数据
 * @param $table ,表名
 * 这里仅获取，name，id
 */
function get_music_find($table= null){
	if(empty($table)){;
		return false;
	}
	//拼接参数
	$info = M($table)->field('id,name')->select();
	return $info;
}

/*
*获取服务器组
*/
function get_server($id=null) {
	$list = S('serverList');
	if (!$list){
		$list= M('Server')->field(true)->order('id desc')->select();
		if (is_array($list)){		
			foreach ($list as $key => $v) {
				$list[$key]['downPath'] = $v['url'].$v['down_dir'];
				$list[$key]['listenPath'] = $v['url'].$v['listen_dir'];
			} 
		}
	}
	if (!empty($id)){
		foreach ($list as $key => $v) {
		 	 if ($id == $v['id']){
		 	 	return $list[$key];
		 	 	break;
		 	 }
		} 
	}else{
		return $list;
	}
}


/*获取设置随机数*/
function setrand ($str) {
	if(!empty($str)){
		$str = @explode("-", $str);
		if(count($str) > 1){
			return  rand(trim($str[0]),trim($str[1])); 
		}else{
			return  trim($str[0]);											
			//return rand(trim($str[0]),trim($str[1]);
		}
	}else{		
		return '0';
	}
}

/*删除文件*/
function rmdirr($dirname) {
	if (!file_exists($dirname)) {
		return false;
	}
	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}
	$dir = dir($dirname);
	if($dir){
		while (false !== $entry = $dir->read()) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		}
	}
	$dir->close();
	return rmdir($dirname);
}


//转换/线
function file_realpath($path){
    $path=str_replace('\\','/',$path);
    if(!is_link($path)) return realpath($path);
    return preg_replace('/[^:]?\/{2,}/si','/',$path);
}

function __basename($path){
    $path=str_replace("\\","/",$path);
    $path=explode("/",$path);
    return file_name_convert($path[count($path)-1]);
}

//文件名转码
function file_name_convert($str){
    if(!function_exists("mb_convert_encoding")) return $str;
    $encode=func_num_args()<2?"UTF-8":func_get_arg(1);
    $enlist=func_num_args()>2?func_get_arg(2):"auto,CP936";
    return mb_convert_encoding($str,$encode,$enlist);
}

/**
 * 获取一个路径(文件夹&文件) 当前文件[夹]名
 * test/11/ ==>11 test/1.c  ==>1.c
 */
function get_path_this($path){
    $path = str_replace('\\','/', rtrim(trim($path),'/'));
    return substr($path,strrpos($path,'/')+1);
} 


// url头部数据
function url_header($url){
	$name = '';$length=0;
	$header = @get_headers($url,true);
	if (!$header) return false;

	if(isset($header['Content-Length'])){
		if(is_array($header['Content-Length'])){
			$length = array_pop($header['Content-Length']);
		}else{
			$length = $header['Content-Length'];
		}
	}
	if(isset($header['Content-Disposition'])){
		if(is_array($header['Content-Disposition'])){
			$dis = array_pop($header['Content-Disposition']);
		}else{
			$dis = $header['Content-Disposition'];
		}
		$i = strpos($dis,"filename=");
		if($i!= false){
			$name = substr($dis,$i+9);
			$name = trim($name,'"');
		}
	}
	if(!$name){
	    $name = get_path_this($url);
	    if (stripos($name,'?')) $name = substr($name,0,stripos($name,'?'));
	    if (!$name) $name = 'index.html';
	}
	// $header['name'] = $name;
	// return $header;
	return array('length'=>$length,'name'=>$name);
}

	/**
	 * 文件下载到服务器
	 */
	function file_download ($from, $file_name){
		$fp = @fopen ($from, "rb");
		if ($fp){
			$new_fp = @fopen ($file_name, "wb");
			while(!feof($fp)){
				fwrite($new_fp, fread($fp, 1024 * 8 ), 1024 * 8);
			}
			return true;
		}else{
			return false;
		}	
	}

/**
 * 获取精确时间
 */
function mtime(){
	$t= explode(' ',microtime());
	$time = $t[0]+$t[1];
	return $time;
}