<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台配置控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class ConfigController extends AdminController {

    /**
     * 配置管理
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        /* 查询条件初始化 */
        $map = array();
        $map  = array('status' => 1);
        if(isset($_GET['group'])){
            $map['group']   =   I('group',0);
        }
        if(isset($_GET['name'])){
            $map['name']    =   array('like', '%'.(string)I('name').'%');
        }

        $list = $this->lists('Config', $map,'sort,id');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('group',C('CONFIG_GROUP_LIST'));
        $this->assign('group_id',I('get.group',0));
        $this->assign('list', $list);
        $this->meta_title = '配置管理';       
        $this->display();
    }

    /**
     * 新增配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->add()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info',null);
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->save()){
                    S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_config','config',$data['id'],UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /**
     * 批量保存配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = M('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        $this->success('保存成功！');
    }

    /**
     * 删除配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Config')->where($map)->delete()){
            S('DB_CONFIG_DATA',null);
            //记录行为
            action_log('update_config','config',$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group() {
        $id     =   I('get.id',1);
        $type   =   C('CONFIG_GROUP_LIST');
        $list   =   M("Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('id',$id);
        $this->meta_title = $type[$id].'设置';
        $this->display();
    }

    /**
     * 配置排序
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');
            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(I('group')){
                $map['group']	=	I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '配置排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！',Cookie('__forward__'));
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
    
    /*
    * 驱动配置
    * @author 战神巴蒂 <378020023.qq.com>
    */
    
   	public function updrive(){
   		if (IS_POST){
   			$config  = I('post.');
						
			foreach( $config as $k => $v ){				
				foreach( $v as $b=>$c ){
					$config[$k][$b]  = trim($c);
				}				
			}
			$text ="<?php\treturn " . var_export($config, true).';';		
			$result = D('File')->writeConfig('up_drive_config',$text);
   			if ($result){
				 S('DB_CONFIG_DATA',null);
   				$this->success('更新成功', U('Config/group'));   				
   			}else{
   				$this->error('更新失败,请检查[Application/Common/Conf/up_drive_config.php]文件是否可写');
   			}
   		}else{
	        $this->meta_title = '上传驱动设置';
	        $this->display();
   	
		}
   	}  	
   	
   /*
    * 视图配置
    * @author 战神巴蒂 <378020023.qq.com>
    */
    public function view($theme=null){
    	$config_file =  './Application/Common/Conf/home_view_config.php';
		$user_config_file = './Application/Common/Conf/user_view_config.php';
    	$home_conf = include $config_file;
    	$user_conf = include  $user_config_file;
    	if (IS_POST){
			if(!empty($theme)){
				$tmpl = '/Template/theme/'.$theme.'/';	
				$teme = array('DEFAULT_THEME' =>  $theme);					
				$tpl  =  array('TMPL_PARSE_STRING' => array(
					'__STATIC__' => __ROOT__ . '/Public/static',
				    '__TMPL__'   => __ROOT__ . $tmpl,
				  ),);			  
				$path = array('VIEW_PATH' =>  '.'. $tmpl);			
   				$config = array_merge($home_conf,$teme,$tpl);
   				$user_config = array_merge($user_conf,$path,$tpl);				
   			}else{
   				$result = false;
   			}
   			$result = file_put_contents($config_file, "<?php\treturn " . var_export($config, true).';');
   			$result1 = file_put_contents($user_config_file, "<?php\treturn " . var_export($user_config, true).';');   				
   			if ($result1 && $result  ){
   				$this->success('更新成功', U());  				
   			}else{
   				$this->error('更新失败,请检查[Application/Common/Conf/home_view_config.php]文件是否可写');
   			}
   		}else{
   			//screenshot.png
   			$dir = './Template/theme/';
   			$file = new \OT\File;
   			$dirs = $file->get_dirs($dir);
   			$view = array();
			foreach ($dirs['dir'] as $k=>$v) {//遍历目录
				if ($v != '.' && $v != '..'){
					$conf = $dir . $v. '/theme.xml';	
					if(file_exists($conf)){
						$view[$k]  = $this->getxml($conf);
						$view[$k]['cover'] = 	$dir . $v. '/screenshot.png';
					}
				} 
			}
			$conf = include './Application/Common/Conf/home_view_config.php';
			$this->assign('theme_name',$conf['DEFAULT_THEME']);
			$this->assign('list',$view);
			$this->assign('home_conf',$home_conf);
			$this->assign('user_conf',$user_conf);
	        $this->meta_title = '视图设置';
	       	$this->display();   	
		}   	
    }
    
   /*
    *  Home 模块配置
    * @author 战神巴蒂 <378020023.qq.com>
    */
    
   	public function homemodule(){
   		$conf = get_custom_config('home_view_config');
   		if (IS_POST){
   			$config  = I('post.');
   			$config = array_merge($conf,$config);
			$text ="<?php\treturn " . var_export($config, true).';';		
			$result = D('File')->writeConfig('home_view_config',$text);			
			if ($result){
				S('DB_CONFIG_DATA',null);
   				$this->success('更新成功');   				
   			}else{
   				$this->error('更新失败,请检查[Application/Common/Conf/home_view_config.php]文件是否可写');
   			}
   		}
   	}
   	
   	/*
    *  User 模块配置
    * @author 战神巴蒂 <378020023.qq.com>
    */
    
   	public function usermodule (){
   		if (IS_POST){
			$conf = get_custom_config('user_view_config');
   			$config  = I('post.');
   			$config = array_merge($conf,$config);
   			$text ="<?php\treturn " . var_export($config, true).';';		
			$result = D('File')->writeConfig('user_view_config',$text);				
			if ($result){
				S('DB_CONFIG_DATA',null);
   				$this->success('更新成功');   				
   			}else{
   				$this->error('更新失败,请检查[/Application/Common/Conf/user_view_config.php]文件是否可写');
   			}
   		}
   	}
    
    public function getxml($file) {
   		$xml = @simplexml_load_file($file);			
		if(is_object($xml)){
			$xml = json_encode($xml);
			$xml = json_decode($xml, true);
		}
		return is_array($xml)?  $xml : null;
    }
           
}