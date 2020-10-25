<?php
namespace app\common\fun;


class Page{
    
    /**
     * wap底部菜单 有可能是 商铺的菜单.
     * @return \app\common\fun\unknown[]|\app\common\fun\unknown
     */
    public function foot_menu(){
        $menu = [];
        $hyid = config('webdb.sys_mode_type')==1 ? get_cookie('last_qun_id') : get_cookie('HYID');        
        if ($hyid) {
            $menu = cache('qun_menu_1_'.$hyid);
            if( empty($menu) ){
                $menu = get_sons(model('qun/menu')->getTreeList(['aid'=>$hyid,'ifshow'=>1,'type'=>1]));
                cache('qun_menu_1_'.$hyid,$menu);
            }
        }
        $menu || $menu = $this->get_web_menu('wapfoot');
        return $menu;
    }
    
    /**
     * 获取网站头部菜单数据
     * @param string $type 可以取值pc或wap
     * @return unknown[]|unknown
     */
    public function get_web_menu($type=''){
        if($type == 'wap'){
            $_type = [0,2];
        }elseif($type == 'wapfoot'){
            $_type = [3];
        }else{
            $type = 'pc';
            $_type = [0,1];
        }
        $array = cache('web_menu_'.$type);
        if (empty($array)) {
            $array = model('admin/webmenu')->getTreeList(['ifshow'=>1,'type'=>['in',$_type]]);
            cache('web_menu_'.$type,$array);
        }
        return get_sons($array);
    }
    
    /**
      * PC面包屑导航
      * @param string $link_name
      * @param string $link_url
      * @param number $fid
      */
     public function getNavigation($link_name='',$link_url='',$fid=0){
         if($link_name&&$link_url){
             if(strpos($link_url,'/')!==0&&strpos($link_url,'http')!==0){
                 list($_path,$_parameter) = explode('|',$link_url);
                 $link_url = iurl($_path,$_parameter);
             }
         }
         $template = getTemplate('index@nav');
         if(is_file($template)){
             include($template);             
         }
//          $path = dirname(config('index_style_layout'));
//          if(IN_WAP===true){
//              if(is_file($file = $path.'/wap_nav.htm')){
//                  include($file);
//              }else{
//                  @include($path.'/nav.htm');
//              }          
//          }else{
//              if(is_file($file = $path.'/pc_nav.htm')){
//                  include($file);
//              }else{
//                  @include($path.'/nav.htm');
//              }
//          }
     }
     
}