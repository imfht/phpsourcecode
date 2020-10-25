<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

class SettingAction extends Action {
	/**
		* 参数列表
		*@param $gid  传入分类ID
		*@param $json    为NULL输出模板。为1时输出列表数据到前端，格式为Json
		*@examlpe 
	*/
    public function index($gid=1,$json=NULL){
		$Public = A('Index','Public');
		$Public->check('Setting',array('r'));
		
		//main
		if(!is_int((int)$json)){
			$json = NULL;
		}
		if($json==1){
			$gid = intval($gid);
			$config = M('Config');
			$info = $config->order('sort asc')->where('gid='.$gid)->order('convert(name using gbk) asc')->select();
			//dump($info);
			$new_info = array();
			foreach($info as $t){
				switch($t['types']){
					case 'bool':
						if($t['vals']==1){
							$t['vals'] = '开启';
						}else{
							$t['vals'] = '关闭';
						}
					break;
					
					case 'select':
						$str_opt = htmlspecialchars_decode($t['opts']);
						$arr_opt = explode('|',$str_opt);
						foreach($arr_opt as $ss){
							if(strstr($ss,'=>')){
								list($sk,$sv) = explode('=>',$ss);
								if($t['vals']==$sk){
									$t['vals'] = $sv;
								}
								unset($sk,$sv);
							}
						}
						unset($str_opt,$arr_opt);
					break;
					
					case 'more':
						$str_opt = htmlspecialchars_decode($t['opts']);
						$arr_opt = explode('|',$str_opt);
						$arr_val = explode(',',$t['vals']);
						$stropt = array();
						foreach($arr_opt as $ss){
							if(strstr($ss,'=>')){
								list($sk,$sv) = explode('=>',$ss);
								if(in_array($sk,$arr_val)){
									$stropt[] = $sv;
								}
								unset($sk,$sv);
							}else{
								$stropt[] = $ss;
							}
						}
						$t['vals'] = implode(',',$stropt);
						unset($str_opt,$arr_opt,$arr_val,$stropt);
					break;
				}
				if($t['notes']){
					$notes = ' <span style="color:#666">'.$t['notes'].'</span>';
				}else{
					$notes = '';
				}
				$t['vals'] = $t['vals'].$notes;
				$new_info[] = $t;
			}
			
			echo json_encode($new_info);
			unset($info,$new_info,$config);
		}else{
			$this->display();
		}
		unset($Public);
    }
	
	/**
		* 新增与更新数据
		*@param $act add为新增、edit为编辑
		*@param $go  为1时，获取post
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function add($act=NULL,$go=false,$id=NULL){	
		$Write = A('Write','Public');
		
		//main
		$config = M('Config');
		if($go==false){
			$this->assign('uniqid',uniqid());
			if($act=='add'){
				$this->assign('act','add');
				$this->display();
			}else{
				if(!is_int((int)$id)){
					$id = NULL;
					$this->show('无法获取ID');
				}else{
					$map['id'] = array('eq',$id);
					$info = $config->where($map)->find();
					unset($map);
					if($info['types']=='between'){
						$info['opts'] = unserialize($info['opts']);
					}elseif($info['types']=='select'){
						$sopt = '';
						$str_opt = htmlspecialchars_decode($info['opts']);
						$arr_opt = explode('|',$str_opt);
						foreach($arr_opt as $ss){
							if(strstr($ss,'=>')){
								list($sk,$sv) = explode('=>',$ss);
								if($info['vals']==$sk){
									$selected = 'selected="selected"';
								}else{
									$selected = '';
								}
								$sopt .= '<option value="'.$sk.'" '.$selected.'>'.$sv.'</option>';
								unset($sk,$sv);
							}else{
								if($info['vals']==$ss){
									$selected = 'selected="selected"';
								}else{
									$selected = '';
								}
								$sopt .= '<option value="'.$ss.'" '.$selected.'>'.$ss.'</option>';
							}
						}
						
						$this->assign('sopt',$sopt);
						unset($str_opt,$arr_opt);
						$sopt = '';
					}elseif($info['types']=='more'){
						$mopt = '';$smopt = array();$selected = '';
						$str_opt = htmlspecialchars_decode($info['opts']);
						$arr_opt = explode('|',$str_opt);
						$arr_val = explode(',',$info['vals']);
						foreach($arr_opt as $ss){
							if(strstr($ss,'=>')){
								list($sk,$sv) = explode('=>',$ss);
								if(in_array($sk,$arr_val)){
									//$smopt[] = $sk;
									$selected = 'selected="selected"';
								}else{
									$selected = '';
								}
								$mopt .= '<option value="'.$sk.'" '.$selected.'>'.$sv.'</option>';
								unset($sk,$sv);
							}else{
								if(in_array($ss,$arr_val)){
									//$smopt[] = $ss;
									$selected = 'selected="selected"';
								}else{
									$selected = '';
								}
								$mopt .= '<option value="'.$ss.'" '.$selected.'>'.$ss.'</option>';
							}
						}
						
						$this->assign('mopt',$mopt);
						$this->assign('smopt',$smopt);
						unset($str_opt,$arr_opt,$arr_val);
						$mopt = '';
					}
					$this->assign('id',$id);
					$this->assign('act','edit');
					$this->assign('info',$info);
					$this->display();
					unset($info);
				}
			}	
		}else{
			$data = $config->create();
			$data['keyword'] = preg_replace("/[\"\']/","",$data['keyword']);
			switch($data['types']){
				case 'char':
					$data['opts'] = strval($data['opts']);
					$data['vals'] = $data['opts'];
				break;
				
				case 'int':
					$data['opts'] = floatval($data['opts']);
					$data['vals'] = $data['opts'];
				break;
				
				case 'bool':
					$data['opts'] = intval($data['opts']);
					$data['vals'] = $data['opts'];
				break;
				
				case 'upload':
					import('ORG.Net.UploadFile');
					$up = new UploadFile();
					$up->allowTypes = array('image/pjpeg','image/jpeg','image/x-png','image/png','image/gif','image/bmp');
					$upload = C('TMPL_PARSE_STRING.__UPLOAD__');
					$up->savePath = ROOT.'/'.$upload.'/';
					$up->charset = 'UTF-8';
					$up->autoSub = true;
					
					if($up->upload()){
						$info = $up->getUploadFileInfo();
						$data['opts'] = $info[0]['savename'];
						$data['vals'] = $data['opts'];
						unset($up);
					}else{
						$errormsg = $up->getErrorMsg();
						$errorno = $up->getErrorNo();
						if($errorno==0){
							if(isset($delimg)){
								$data['opts'] = '';
							}
						}else{
							echo 0;
							exit;
						}
					}
				break;
				
				case 'between':
					$data['opts'][0] = floatval($data['opts'][0]);
					$data['opts'][1] = floatval($data['opts'][1]);
					$data['opts'] = serialize($data['opts']);
					$data['vals'] = implode(',',$data['opts']);
				break;
				
				case 'text':
					$data['opts'] = htmlspecialchars($data['opts']);
					$data['vals'] = $data['opts'];
				break;
				
				case 'select':
					if(isset($data['opts'])){
						$data['opts'] = htmlspecialchars($data['opts']);
					}
				break;
				
				case 'more':
					if(isset($data['opts'])){
						$data['opts'] = htmlspecialchars($data['opts']);
					}else{
						$data['vals'] = implode(',',$data['vals']);
					}
				break;
			}
			
			//dump($data);exit;
			if($act=='add'){
				$Public = A('Index','Public');
				$role = $Public->check('Setting',array('c'));
				if($role<0){
					echo $role; exit;
				}
				$keywork = $config->where('keywork=\''.$data['keyword'].'\'')->count();
				if($keywork){
					echo -99; exit;
				}
				
				$add = $config->add($data);
				if($add>0){
					$info = $config->field('types,keyword,vals')->select();
					$path = ROOT.'/Conf/appcfg.php';
					$Write->write($path,$info,'conf');
					echo 1;
				}else{
					echo 0;
				}
				unset($info,$path,$Public,$data);
			}elseif($act=='edit'){
				$Public = A('Index','Public');
				$role = $Public->check('Setting',array('u'));
				if($role<0){
					echo $role; exit;
				}
				
				if(!is_int((int)$id)){
					echo 0;
				}else{
					$otypes = I('otypes');
					$oopts = I('oopts');
					$delimg = I('delimg');
					
					import('ORG.Net.FileSystem');
					$sys = new FileSystem();
					$upload = C('TMPL_PARSE_STRING.__UPLOAD__');
					$path = ROOT.'/'.$upload.'/'.$oopts;
					
					$map['id'] = array('eq',$id);
					if($data['opts']==$oopts || $data['opts']===''){
						unset($data['opts']);
						unset($data['vals']);
					}
					
					if($data['types']=='upload'){
						if($delimg==1){
							if($oopts){
								if(file_exists($path)){
									$df = $sys->delFile($path);
									if($df){
										$data['opts'] = '';
										$data['vals'] = '';
									}
								}
							}
						}
					}
					$edit = $config->where($map)->save($data);
					unset($map);
					if($edit !== false){
						if($otypes=='upload' && $data['types']!=$otypes){
							if($oopts){
								if(strstr($path,'.')){
									$sys->delFile($path);
								}
							}
						}
						
						if($oopts && $data['opts']){
							if(strstr($path,'.')){
								$sys->delFile($path);
							}
						}
						
						$info = $config->field('types,keyword,vals')->select();
						$path = ROOT.'/Conf/appcfg.php';
						$Write->write($path,$info,'conf');
						echo 1;
					}else{
						echo 0;
					}
				}
				unset($sys,$path,$Public,$data,$upload);
			}
		}
		unset($config,$Write);
	}
	
	/**
		* 删除数据
		*@param $id  数据Id
		*@examlpe 
	*/
	public function del($id){
		$Public = A('Index','Public');
		$role = $Public->check('Setting',array('d'));
		if($role<0){
			echo $role; exit;
		}
		
		//main
		if(!is_int((int)$id)){
			echo 0;
		}else{
			$config = M('Config');
			$map['id'] = array('eq',$id);
			$sys = $config->where($map)->getField('sys');
			if($sys){
				$del = $config->where($map)->delete();
				unset($map);
				if($del){
					echo 1;
				}else{
					echo 0;
				}
			}else{
				echo 2;
			}
			unset($config);
		}
		unset($Public);
	}
	
	/**
		* 生成配置文件
		*@examlpe 
	*/
	public function json(){
		$Write = A('Write','Public');
		
		//main
		$config = M('Config');
		$info = $config->field('types,keyword,vals')->select();
		$path = ROOT.'/Conf/appcfg.php';
		$ww = $Write->write($path,$info,'conf');
		if($ww){
			echo 1;
		}else{
			echo 0;
		}
		unset($info,$path,$config,$Write);
	}
}