<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class FileManageController extends AdminBaseController {

    public function index(){

        //获取图标数组
        $ext_path ='Public/Img/ext/';
        $ext_file = glob($ext_path . '*.*');
        foreach ($ext_file as $k => $v) {
            $key=current(explode('.',basename($v)));
            $ext[$key]=__ROOT__.'/'.$v;
        }

        //获取当前目录及上级目录
        $root=I('root','Application/Home/View','trim');
        $this->assign('root',$root);

        //$path_root=str_replace('~','/',$root);
        $path_root=$root;

        $dir=I('dir','','trim');
        if(empty($dir)){
        	$path_now=$path_root;
        	$back='';
        }else{

            //$path_now=$path_root.'/'.str_replace(array('~','#'),array('/','.'),$dir);
            $path_now=$path_root.'/'.$dir;

            //$back=substr($dir,0,strrpos($dir,'~'));//上级目录
            $back=substr($dir,0,strrpos($dir,'/'));
        }
        $this->assign('back',$back);

        //获取文件列表
        $list = glob($path_now . '/*');
        if (!empty($list)) ksort($list);

        //过滤文件列表
        $allow_edit=array('php','js','css','html','htm');
        $file_list=array();
        foreach ($list as $k => $v) {
            $v=str_replace($path_root.'/','',$v);
            //获取拓展名
            $this_ext = pathinfo($v, PATHINFO_EXTENSION);
            //没有拓展名,判断为文件夹
            if(empty($this_ext)) $this_ext = 'dir';
            //判断是否允许编辑
            if(in_array($this_ext, $allow_edit)) $file_list[$k]['is_edit']=1;
            else $file_list[$k]['is_edit']=0;

            $file_list[$k]['ext']=$this_ext;
            $file_list[$k]['ext_img']=$ext[$this_ext] ? $ext[$this_ext] : $ext['hlp'];

            //$file_list[$k]['dir']=str_replace(array('/','.'),array('~','#'),$v);
            $file_list[$k]['dir']=$v;

            $file_list[$k]['name']=basename($v);

        }
        $this->assign('path_root',$path_root);
        $this->assign('path_now',$path_now);
        $this->assign('path_return',$path_return);
        $this->assign('list', $file_list);
        $this->display();
    }

    public function add($root){
        if(IS_POST){ $this->addPost($root,$dir);exit; }
        $dir=I('dir','');

        // if(empty($dir)) $path=str_replace('~','/',$root).'/';
        // else $path=str_replace('~','/',$root).'/'.str_replace(array('~','#'),array('/','.'),$dir).'/';
        if(empty($dir)) $path=$root.'/';
        else $path=$root.'/'.$dir.'/';

        $this->assign('path',$path);
        $this->display();
    }
    private function addPost($root,$dir){

        $post=I('post.','','trim');
        //判断文件类型,为空追加默认扩展名
        $ext = pathinfo($post['title'], PATHINFO_EXTENSION);
        if(empty($ext)) $post['title'].='.html';

        $file=$post['path'].$post['title'];
        $content=$post['content'];
        //判断同名文件
        if(is_file($file)) $this->error('同名文件存在');
        //添加文件
        if(file_put_contents($file, $content)){
            $this->success('文件添加成功',U('index')."?root={$post['root']}&dir={$post['dir']}");
        }else{
            $this->error('文件添加失败');
        }
    }

    public function edit($root,$dir){
        if(IS_POST){ $this->editPost($root,$dir);exit; }

        //$info['file']=str_replace('~','/',$root).'/'.str_replace(array('~','#'),array('/','.'),$dir);
        $info['file']=$root.'/'.$dir;

        $info['title']=basename($info['file']);
        $info['content']=file_get_contents($info['file']);

        $this->assign('info',$info);
        $this->display();
    }
    public function editPost($root,$dir){

        $post=I('post.','','trim');
        $file=$post['file'];
        $content=$post['content'];

        //判断文件类型,为空追加默认扩展名
        $ext = pathinfo($post['title_new'], PATHINFO_EXTENSION);
        if(empty($ext)) $post['title_new'].='.html';

        //判断是否需要重命名
        if($post['title_new'] != $post['title']){
            $dirname=dirname($post['file']);
            rename($dirname.'/'.$post['title'], $dirname.'/'.$post['title_new']);
            $file=$dirname.'/'.$post['title_new'];
        }
        //修改文件
        //$back=substr($post['dir'],0,strrpos($post['dir'],'~'));
        $back=substr($post['dir'],0,strrpos($post['dir'],'/'));

        if(file_put_contents($file, $content)){
            $this->success('文件修改成功',U('index')."?root={$post['root']}&dir={$back}");
        }else{
            $this->error('文件修改失败');
        }
    }

    public function del($root,$dir){
        // $path_root=str_replace(array('~'),array('/'),$root);
        // $path_dir=str_replace(array('~','#'),array('/','.'),$dir);
        // $file=$path_root.'/'.$path_dir;
        $file=$root.'/'.$dir;

        if(is_file($file)){
            if(unlink($file)){
                $this->success('删除'.basename($file).'文件成功');
            }else{
                $this->error('删除'.basename($file).'文件失败');
            }
        }
        
    }


}