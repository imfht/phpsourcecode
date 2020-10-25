<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\build\builder;

class Index extends AdminBase
{
  public function index(){
    $data = $this->request->param();
    $topmenuname = $data['topmenuname']?$data['topmenuname']:"home_action";
  	//菜单
  	$defaultmenu = config('adminmenu');
  	$Models = finddirfromdir(APP_PATH."../application");
  	$no_join = ['ajax','index','asset','common','post','ucenter','admin'];
    //模型
    	foreach ($Models as $k => $v) {
    		$path = APP_PATH."../application/".$v."/menu.php";
    		if(!in_array($v, $no_join)&&file_exists($path)){
    			$menu= include($path);
    			$defaultmenu['adminmenu']['child'][]=$menu['adminmenu'];
    			
    		}
  	}
  
    //插件
      if($topmenuname=="admin_plus_model"){
        
        // $Plus = finddirfromdir(APP_PATH."../plus");
        // if(is_array($Plus)){
        //   foreach ($Plus as $k => $v) {
        //     $path = APP_PATH."../plus/".$v."/config.php";
        //     if(file_exists($path)){
        //       $menu= include($path);
        //       if($menu['plusmenu']){
        //         $defaultmenu['plus']['child'][]=$menu['plusmenu'];
        //       }
              
        //     }

        //   }
        // }
      }

      //无限分类处理
      $html = $this->treeMenu($defaultmenu);
      $this->assign('defaultmenu',$defaultmenu);
      $this->assign('html',$html);


  	return $this->fetch('admin/index/index');
  }

  private function treeMenu($menu,$parentkey=0,$level = 0,$parenturl=""){
    $clss = $level>0?"collapse in two-level-menu":"list-group";
    $id   = $level>0?"":"mainnav-menu";
    $html = '<ul class="'.$clss.'" id="'.$id.'">';
    $level++;
      foreach ($menu as $k => $v) {
        if(is_array($v['child'])){
          $html .= '<li class="" url="'.url($v['url']).'" key="'.$k.'" levelid="'.$v['url'].'-'.$k.'-'.$level.'" parentlevelid="'.$parenturl.'-'.$parentkey.'-'.($level-1).'" parentkey="'.$parentkey.'" level="'.$level.'" parentlevel="'.$parentlevel.' " ><a href="javascript:;" > <i class="fa '.$v['ico'].'"></i><span class="menu-title">'.lang($v['name']).'</span><i class="arrow"></i></a>';
          $html.=self::treeMenu($v['child'],$k,$level);
          $html.='</li>';

        }else{
          $html .= '<li class="checkmenu" url="'.url($v['url']).'"  levelid="'.$v['url'].'-'.$k.'-'.$level.'" parentlevelid="'.$parenturl.'-'.$parentkey.'-'.($level-1).'" key="'.$k.'" parentkey="'.$parentkey.'" level="'.$level.'" parentlevel="'.$parentlevel.' " ><a href="javascript:;" > <i class="fa '.$v['ico'].'"></i><span class="menu-title">'.lang($v['name']).'</span></a></li>';
        }
        
      }
      $html.="</ul>";
      return $html;
  }
  public function toMain(){

  	return $this->fetch('admin/index/tomains');

  }
 }
