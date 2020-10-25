<?php
namespace app\home\controller;

use app\common\controller\Home;

class Download extends Home
{
    public function initialize()
    {
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    
    public function view()
    {
        call_user_func(array('parent',__FUNCTION__)); 
        if(empty($this->assign->data['content'])) $this->download();
    }
    
    function download()
    {
        
        $id=intval($this->args['id']);
		if (empty($id)) {
            return $this->notFound();
		}
			
        
        $data = $this->mdl->field(['id', 'file', 'file_name', 'size', 'link'])->where('id', $id)->find();
        if (empty($data)) {
            return $this->notFound();
        }
			
        $data = $this->mdl->getArray($data);
        
        
        if (empty($data['file']) || !file_exists(WWW_ROOT.$data['file'])) {
			if (!empty($data['link'])) {
                $this->redirect($data['link']);
				exit;
			} else {
				return $this->message('error', '文件不存在');
			}
			exit('该文件不存在');
		}
        
        //下载统计
        if ($this->mdl->form['download_count']) {
            $this->mdl->where('id', $id)->setInc('download_count');
        }
        
        $ua = $_SERVER["HTTP_USER_AGENT"];		
		if (preg_match("/MSIE/", $ua)) {
			$filename = urlencode($data['file_name']);  
			$filename = str_replace("+", "%20", $filename);  
		}else {  
			$filename = $data['file_name'];
		}
        
        $content_url = WWW_ROOT . $data['file'] ;//下载文件地址,可以是网络地址,也可以是本地物理路径或者虚拟路径
        ob_end_clean(); //函数ob_end_clean 会清除缓冲区的内容，并将缓冲区关闭，但不会输出内容。
        header("Content-Type: application/force-download;"); //告诉浏览器强制下载
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$data['size']);
        header("Content-Disposition: attachment; filename=$filename"); 
        header("Expires: 0");
        header("Cache-control: private");
        header("Pragma: no-cache"); //不缓存页面
        readfile($content_url);         
        exit;
    }
}
