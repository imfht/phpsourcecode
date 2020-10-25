<?php
namespace app\admin\controller;

die('此文件作废.可以删除!');
use app\common\controller\AdminBase;
use think\Controller;
use app\common\model\Config AS ConfigModel;

class Center extends AdminBase
{
	
	
	public function config()
	{
		if(IS_POST){
			$data = get_post('post');
			$model = new ConfigModel();
			if ( $model->save_data($data['webdbs']) ) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
		}
		$this->assign('truepath', '');
		$this->assign('select_style', '');
		return $this->fetch();
	}
	
	public function homepage()
    {
		if(IS_POST){
			$data = get_post('post');
			$model = new ConfigModel();
			
			if ( $model->save_data(  $this->format_homepage($data)  ) ) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
		}
		return $this->fetch();
	}
	
	
	/**
     * 空操作，用于显示其它任何操作
     */
	public function _empty()
    {
		if(IS_POST){
			$data = get_post('post');
			$model = new ConfigModel();
			if ( $model->save_data($data['webdbs']) ) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
		}
		
		$model_name = $this->request->action();

		return $this->fetch($model_name);
	}

	private function format_homepage($data)
    {
		extract($data);
		if($webdbs['homepage_banner'] && $webdbs['homepage_banner']!=$webdb['homepage_banner']){
			delete_attachment($lfjuid,tempdir($webdb['homepage_banner']));
		}
		if($webdbs['homepage_logo'] && $webdbs['homepage_logo']!=$webdb['homepage_logo']){
			delete_attachment($lfjuid,tempdir($webdb['homepage_logo']));
		}
		
		$rArray = unserialize(stripslashes($webdb['homepage_rollpic']));
		foreach($rArray AS $key=>$rs){
			if($post_db[$key]['pic']!=$rs['pic']){
				delete_attachment($lfjuid,tempdir($rs['pic']));
			}
		}

		$rArray = unserialize(stripslashes($webdb['homepage_adimages']));
		foreach($rArray AS $key=>$rs){
			if($adimages[$key]['pic']!=$rs['pic']){
				delete_attachment($lfjuid,tempdir($rs['pic']));
			}
		}
		
		$rArray = unserialize(stripslashes($webdb['homepage_menuTop']));
		foreach($rArray AS $key=>$rs){
			if($menu_top[$key]['pic']!=$rs['pic']){
				delete_attachment($lfjuid,tempdir($rs['pic']));
			}
		}
		
		$rArray = unserialize(stripslashes($webdb['homepage_menuFoot']));
		foreach($rArray AS $key=>$rs){
			if($menu_foot[$key]['pic']!=$rs['pic']){
				delete_attachment($lfjuid,tempdir($rs['pic']));
			}
		}
		
		$array = '';
		foreach($post_db AS $rs){
			if($rs['pic']){
				$array[] = $rs;
			}
		}

		$array1 = '';
		foreach($adimages AS $rs){
			if($rs['pic']){
				$array1[] = $rs;
			}
		}

		$webdbs['homepage_rollpic'] = addslashes(serialize($array));

		$webdbs['homepage_adimages'] = addslashes(serialize($array1));
		
		$webdbs['homepage_menuTop'] = addslashes(serialize($menu_top));
		$webdbs['homepage_menuFoot'] = addslashes(serialize($menu_foot));
		return $webdbs;
	}
}
