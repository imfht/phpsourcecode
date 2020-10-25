<?php
namespace app\center\Logic;
use app\center\Logic\Controller;
class Photo extends Controller{
    public function init(&$opts,$action){
        if($action == 'index'){
            $js = $opts['js'];
            $js[] = 'index/photo';
            $opts['js'] = $js;
        }
    }
    public function main()
    {     
        $res = $this->getPhotos();
        $pages = [
            'html' => $res['html'],
            'count'=> $res['count']
        ];
        $this->assign('pages',$pages);
        return $this->fetch('photo');
    }
    // 获取ajax
    private function getPhotos($page=1)
    {
        $app = $this->app;
        $uInfo = uInfo();$num = 40;
        $count = $app->croDb('sys_file')->where(['user_code'=>$uInfo['code'],'file_use'=>'P1'])->count();
        $html = '';$imgs = '';$allPage = 0;
        if($count>0){
            $allPage = ceil($count/$num);
            $data = $app->croDb('sys_file')->where(['user_code'=>$uInfo['code'],'file_use'=>'P1'])->field('file_id,file_name,url_name')->page($page,$num)->order('edittm desc')->select();
            $i = 1;            
            foreach($data as $v){
                $imgs .= '<a href="javascript:void(0);" onClick="PT.about(this)" title="'.$v['file_name'].'" dataid="'.$v['file_id'].'" v-row="'.$i.'"><img src="/conero/files/'.$v['url_name'].'" class="img-thumbnail" style="width: 140px; height: 140px;"></a>';
                $i ++;
            }
            if($imgs) $html = '<h4 class="text-right">'.$page.'/<span '.($page == 1? 'id':'class').'="page_max">'.$allPage.'</span></h4>
                    <div dataid="'.$page.'" class="photo_wall">'.$imgs.'</div>';
        }
        return ['allpage'=>$allPage,'page'=>$page,'count'=>$count,'html'=>$html];
    }
    public function ajax()
    {
        $item = isset($_POST['item'])? $_POST['item']:'';
        $result = '--CONERO--BY-- JOSHUA DOEEKING';
        if($item == 'more_photos'){
            $res = $this->getPhotos(intval($_POST['page']));
            $result = $res['html'];
        }
        echo $result;
    }
    // 数据保存
    public function save(){  
        $formid = isset($_POST['formid'])? $_POST['formid']:'';
        if('upphotos' == $formid){  // 文件上传 - 表单上传
            $saveData = [
                'file_own' => '',
                'file_use' => 'P1'
            ]; 
            $sf = uLogic('Sysfile');

            $sf->uploadPlusData($saveData);
            if($sf->upload()) $this->success('文件上传成功！',urlBuild('!.','?photo'));
        }
        elseif('ups4url' == $formid){
            $data = $_POST;
            // println($data);
            $url = $data['url'];
            $saveData = [
                'file_own'  => '',
                'file_use' => 'P1',
                'remark'    => '图片来源网站: '.$url
            ];
            if(!empty($data['file_desc'])) $saveData['file_desc'] = $data['file_desc'];

            $url = $data['url'];
            $name = $data['file_name'];
            $name = $name? $name:null;

            $sf = uLogic('Sysfile');
            $sf->uploadPlusData($saveData);
            if($sf->fromUrl($url,$name)) $this->success('相册保存成功！',urlBuild('!.','?photo'));
        }
        // $sf->formUrl();
    }
}