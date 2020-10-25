<?php
// 上传控制器
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------

namespace tpvue\admin\library;

use tpvue\admin\model\FileModel;
use think\Request;

class Upload
{

	protected $request;
	/**
     * 架构函数
     * @param Request $request Request对象
     * @access public
     */
    public function __construct()
    {
		$this->request    = Request();
    }

	/**
	 * 上传控制器
	 */
	public function upload() {

		$upload_type = $this->request->get('uploadtype', 'picture', 'trim');
		$setpath     = $this->request->param('setpath', 'picture', 'trim');
		$config      = config($upload_type.'_upload');
		$rootPath    = $setpath!='picture' && $setpath ? './uploads/'.$setpath : $config['rootPath'];

		$upload_path = $rootPath.'/'.call_user_func_array($config['subName'][0],[$config['subName'][1],time()]);
		$file        = $this->request->file('file');
		$info        = $file->validate(['size'=>$config['maxSize'],'ext'=>$config['exts']])->rule($config['saveName'])->move($upload_path, true, false);

		if ($info) {
			$upload_info      = $this->parseFile($info);
			$save_upload_name = $upload_info['path'];
			if (config('oss_enable')==1) {
				oss_uploadFile($save_upload_name);//存储到静态资源服务器
			}
			$return['status'] = 1;
			$return['info']   = $upload_info;
		} else {
			$return['status'] = 0;
			$return['info']   = $file->getError();
		}

		return $return;
	}

	/**
	 * 上传用户头像
	 * @param  integer $uid [description]
	 * @return [type]       [description]
	 */
	public function uploadAvatar($uid=0)
	{
		if (!$uid) return false;
		$config = config('avatar_upload');
		
		$upload_path = $config['rootPath'].'/'.$uid;
		// 获取表单上传文件 例如上传了001.jpg
		$file = $this->request->file('file');
		$info = $file->validate(['size'=>$config['maxSize'],'ext'=>$config['exts']])->rule($config['saveName'])->move($upload_path, true, false);
		if ($info) {
			$return['status'] = 1;
			$return['info']   = $info;
		} else {
			$return['status'] = 0;
			$return['info']   = $file->getError();
		}

		return $return;
	}

	/**
	 * 保存上传的信息到数据库
	 * @var view
	 * @access public
	 */
	public function save($config, $from_file_name, $file)
    {
        $file = $this->parseFile($file);
        $file['member_id'] = is_login();
        $file['location'] = $config['driver'];
        $file['status'] = 1;
        $info = FileModel::get(['md5' => $file['md5'], 'sha1' => $file['sha1']]);
        if ($info) {
            $info = $info->toArray();
            $info['msg'] = '文件已存在';
            return $info;
        } else {
            $info->save($file);
            return $info;
        }

    }

	protected function parseFile($info) {
		$data['create_time'] = $info->getATime(); 
		$data['basename']    = $info->getBasename();
		//$data['c_time']      = $info->getCTime(); 
		$data['ext']         = $info->getExtension();
		$data['name']        = $info->getFilename();
		$data['mime_type']    = strstr($info->getMime(),'/',true);
		$data['savepath']    = $info->getPath();
		$data['path'] = str_replace("\\", '/', substr($info->getPathname(), 1)); 
		$data['url']         = config('oss_enable')==1 ? config('oss_alias_url').$data['path'] : $data['path'];
		$data['size']        = $info->getSize();
		$data['md5']         = md5_file($info->getPathname());
		$data['sha1']        = sha1_file($info->getPathname());
		return $data;
	}
}