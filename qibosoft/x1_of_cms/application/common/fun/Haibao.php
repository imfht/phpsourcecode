<?php
namespace app\common\fun;

class Haibao{
    
    /**
     * 带分销的宣传二维码
     * @param array $userdb 当前登录用户UID
     * @param number $id 内容ID
     * @param string $logo 二维码LOGO
     * @return string
     */
    public function img($userdb=[],$id=0,$logo=''){
        $share_url = get_url(auto_url('content/show',['id'=>$id]));
        if (!strstr($share_url,'p_uid=')) {
            if(strstr($share_url,'?')){
                $share_url .= '&';
            }else{
                $share_url .= '?';
            }
            $share_url .= 'p_uid='.$userdb['uid'];
        }
        if ($logo=='' && $userdb['icon']) {
            $logo = tempdir($userdb['icon']);
        }
        return get_qrcode($share_url,$logo);
    }
    
    /**
     * 前台发布页选择风格
     * @param number $mid
     * @param number $fid
     * @return unknown[][]
     */
    public function get_haibao_style($mid=0,$fid=0){
        $sort_config = $fid ? get_sort($fid,'config') : [];
        $model_config = model_config($mid);
        $array = [];
        if ($sort_config['haibao']) {
            $array = explode(',',$sort_config['haibao']);
        }
        if ($model_config['haibao']) {
            $array = array_merge($array,explode(',',$model_config['haibao']));
        }
        $data = [];
        foreach($array AS $value){
            if (empty($value) || $data[$data]) {
                continue;
            }
            $info = @include(TEMPLATE_PATH.'haibao_style/'.dirname($value).'/info.php' );
            $data[$value] = [
                    'path'=>$value,
                    'name'=>$info['name']?:$value,
            ];
        }
        return $data;
    }
    
    /**
     * 后台模型或者是栏目设置多个可选择海报风格
     */
    public function get_haibao_list(){
        $show = '<div>请选择:';
        $dir = opendir(TEMPLATE_PATH.'haibao_style');
        while(($file = readdir($dir))!==false){
            if($file!='.' && $file!='..' && is_file(TEMPLATE_PATH.'haibao_style/'.$file.'/info.php')){
                $info = include(TEMPLATE_PATH.'haibao_style/'.$file.'/info.php');
                $show .= '<span style="border:1px solid orange;padding:3px;margin-right:10px;cursor:pointer;" onclick=\'if($("#atc_haibao").val()==""){$("#atc_haibao").val("'.$file.'/show.htm")}else{$("#atc_haibao").val($("#atc_haibao").val()+","+"'.$file.'/show.htm")}\'>'.$info['name'].'</span>';
            }
        }
        $show .= '</div>';
        return $show;
    }
    
}