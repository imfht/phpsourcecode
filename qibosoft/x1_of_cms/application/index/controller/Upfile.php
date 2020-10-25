<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Controller;
//上传功能
class Upfile extends IndexBase
{
    /**
     * 框架或者是点击弹窗的上传功能
     * @param string $fn 回调函数
     * @param string $par 回调函数中用到的参数
     * @return mixed|string
     */
    public function index($fn='upfile',$par='')
    {
        if (empty($this->user)) {
            $this->error("你还没登录");
        }
        if(IS_POST){
            $obj = new Attachment();
            $o = $obj->upload('pop');
            $info = $o->getData();
            if (preg_match('/^\/public\//', $info['path'])) {
                empty($info['url']) && $info['url'] = $info['path'];
                $info['path'] = str_replace('/public/', '', $info['path']);
            }
            $this->assign('info',$info);
            $this->assign('fn',$fn);
            $this->assign('par',$par);
        }
		return $this->fetch();
    }
    
    /**
     * 上传图片
     * @param string $fn
     * @param string $img
     * @return mixed|string
     */
    public function images($fn='end_upfile_images',$img=''){
        $this->assign('img',$img);
        $this->assign('fn',$fn);
        return $this->fetch();
    }
    
    /**
     * 上传文件
     * @param string $fn
     * @param string $par
     * @param number $size
     * @param string $ext
     * @return mixed|string
     */
    public function file($fn='upfile',$par='',$size=0,$ext=''){
        $this->assign('fn',$fn);
        $this->assign('size',$size?$size*1024:0);
        $this->assign('ext',$ext);
        $this->assign('par',$par);
        return $this->fetch();
    }
}
