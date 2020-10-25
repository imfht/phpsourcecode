<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Resources as M;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 静态资源空间控制器
 */
class Resources extends Base{
	/**
	 * 进入主页面
	 */
    public function index(){
    	return $this->fetch();
    }
    /**
     * 获取概况  
     * 后台商城消息 编辑器中的图片只记录上传图片容量  删除相关数据时无法标记图片为已删除状态
     */
    public function summary(){
    	$m = new M();
    	$data = $m->summary();
        return WSTReturn("", 1,$data);
    }
    /*
     * 获取要处理的图片信息
     */
    public function getPicInfo(){
        $m = new M();
        $rs = $m->getPicInfo();
        return $rs;
    }
    /*
     * 图片处理
     */
    public function picHandle(){
        $m = new M();
        $rs = $m->picHandle();
        return $rs;
    }
    /**
	 * 进入列表页面
	 */
    public function lists(){
    	$datas = model('Datas')->listQuery(3);
    	$this->assign('datas',$datas);
    	$this->assign('keyword',input('get.keyword'));
    	$this->assign("p",(int)input("p"));
    	return $this->fetch('list');
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return WSTGrid($m->pageQuery());
    }
    /**
     * 检测图片信息
     */
    public function checkImages(){
    	$imgPath = input('get.resPath');
    	$m = WSTConf('CONF.wstMobileImgSuffix');
	    $imgPath = str_replace($m.'.','.',$imgPath);
	    $imgPath = str_replace($m.'_thumb.','.',$imgPath);
	    $imgPath = str_replace('_thumb.','.',$imgPath);
	    $imgPath_thumb = str_replace('.','_thumb.',$imgPath);
	    $mimg = '';
	    $mimg_thumb = '';
	    if($m!=''){
		    $mimg = str_replace('.',$m.'.',$imgPath);
		    $mimg_thumb = str_replace('.',$m.'_thumb.',$imgPath);
	    }
    	$data['imgpath']=$imgPath;
    	$data['img']= WSTCheckResourceFile($imgPath);
    	$data['thumb']=WSTCheckResourceFile($imgPath_thumb);
    	$data['thumbpath']=$imgPath_thumb;
    	$data['mimg']=WSTCheckResourceFile($mimg);
    	$data['mimgpath']=$mimg;
    	$data['mthumb']=WSTCheckResourceFile($mimg_thumb);
    	$data['mthumbpath']=$mimg_thumb;
    	return $this->fetch('view',$data);
    }
    /**
     * 检测视频信息
     */
    public function checkVideo(){
    	$respath = input('get.resPath');
    	$data['exists'] = WSTCheckResourceFile($respath);
    	$data['respath'] = $respath;
    	return $this->fetch('videoView',$data);
    }
    /**
     * 删除
     */
    public function del(){
        $m = new M();
        return $m->del();
    }  
}
