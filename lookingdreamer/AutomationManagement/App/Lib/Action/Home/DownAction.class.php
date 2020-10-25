<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class DownAction extends BaseAction{
	
    public function _initialize() {
        parent::_initialize();
				// 标签列表
        $Tag = M("Tag");
        $list  = $Tag->where("module='Down'")->field('id,name,count')->order('count desc')->limit('0,25')->select();
        $this->assign('tags',$list);
    }
    	
    // 首页
    public function index()
    {
        $this->assign('title','下载');
        $this->_list('Down','status=1');
        $this->display();
    }

    // 下载
    public function download()
    {
        $id         =   intval($_GET['download']);
        $Down        =   M("Down");
        $map['id'] =  $id;
        if($Down->where($map)->find()) {
            $filename   =   './Public/Uploads/Down/'.$Down->attachfile;
            if(is_file($filename)) {
                $showname = auto_charset($Down->filename,'utf-8','gbk');
                if(!isset($_SESSION['downloadattach_'.$id])) {
                    // 下载计数
                    $Down->where('id='.$id)->setInc('download_count');
                    $_SESSION['downloadattach_'.$id]   =  true;
                }
                import("ORG.Net.Http");
                Http::download($filename,$showname);
            }else{
                $this->error('附件不存在或者已经删除！');
            }
        }else{
            $this->error('附件不存在或者已经删除！');
        }
    }

    // 查看内容
    public function __empty($method){
        if(is_numeric($method)) {
            $vo   =  $Down->where('status=1')->find($method);
            if(!$vo) {
                $this->_404('访问的内容不存在或已经删除！');
            }
            $this->title  =  $vo['title'];
            $this->assign('vo',$vo);
            $this->display('read');
        }elseif(in_array(strtolower($method),array('swf','pdf','rar','7z','zip','chm','gif','jpg','png'))){
            $Down = M("Down");
            $list   =  $Down->where('status=1')->select();
            $this->assign('cates',$list);
            // 分格式查看下载
            $this->assign('type',1);
            $map['module']   =  'Down';
            $map['extension'] = strtolower($method);
            $map['status'] = 1;
            $this->_list('Attach',$map,'id',false);
            $this->title  =  '[ '.strtoupper($method).'格式 ]';
            // 热门下载
            $Attach  =  M("Attach");
            $map['status'] = 1;
            $map['module']   =  'Down';
            $list   =  $Attach->where($map)->field('id,name,size,download_count')->order('download_count desc')->limit('8')->select();
            $this->assign('hots',$list);
            $vo['content']   =  strtoupper($method).'格式下载';
            $vo['title'] =  '';
            $this->assign('vo',$vo);
            $this->display('read');
        }else{
            $this->_404('错误操作');
        }
    }

}
?>