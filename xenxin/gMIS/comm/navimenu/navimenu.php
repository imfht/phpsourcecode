<?php

$myNavi = new PageNavi();
$pnscTodo = "istate in (1,2) and (touser like '".$user->getId()."' or togroup like '".$user->getGroup()
	."' or triggerbyparentid like '".$user->getId()."' or triggerbyparent like '".$user->getGroup()."')";
	# and pid=0 
$pnsckTodo = $myNavi->signPara($pnscTodo);
$pnscTodo = base62x($pnscTodo);

$pnscDone = "istate in (-2,-1,0) and (touser like '".$user->getId()."' or togroup like '".$user->getGroup()
	."' or triggerbyparentid like '".$user->getId()."' or triggerbyparent like '".$user->getGroup()."')";
	# and pid=0 
$pnsckDone = $myNavi->signPara($pnscDone);
$pnscDone = base62x($pnscDone);

$dynamicmenu = '';
$hm = $myNavi->execBy("select levelcode,linkname,modulename,dynamicpara,disptitle,thedb from "
	.$_CONFIG['tblpre']."info_menulist where istate=1 order by levelcode", null,
	$withCache=array('key'=>'navimenu-select'));

#print_r($hm);
if($hm[0]){
    $hm = $hm[1];
    $hmkeys = array();
    $hmkeysbylen = array("2"=>array(),"4"=>array(),"6"=>array(),"8"=>array(),
            "10"=>array(), "12"=>array(), "14"=>array());
    if(is_array($hm)){
		foreach($hm as $k=>$v){
			$hmkeys[$hm[$k]['levelcode']] = $hm[$k]; # use levelcode 作为key
			$hmkeysbylen[strlen($hm[$k]['levelcode'])][] = $hm[$k]['levelcode']; # 按levelcode的长度划分menu等级
		}
    }
    #print_r($hmkeys);
    #print_r($hmkeysbylen);
    for($li = 0; $li <= 99; $li++){
        $li = "".sprintf("%02d",$li);
        if(in_array($li, $hmkeysbylen[2])){
            $linfo = $hmkeys[$li];
            $dynamicmenu .= '<li><!--'.$li.'.--><a href="'.$url.'&navidir='.$linfo['levelcode']
                .'" orighref="javascript:void(0);" class="menulink">'.$linfo['linkname'].'</a>'."\n";
            $dynamicmenu .= "<ul>\n";
            $lv3 = '';
            $lv4 = ''; # @todo, lv5, lv6, lv7
            foreach($hmkeysbylen[4] as $k1=>$v1){
                if(strpos($v1,$li) === 0){
                    $linfo = $hmkeys[$v1];
                    #$dynamicmenu .= 'level2- v1:'.$v1.", \n";
                    if($linfo['modulename'] == '' && $linfo['dynamicpara'] == ''){
                        $dynamicmenu .= '<li><a href="'.$url.'&navidir='.$linfo['levelcode'].'" class="sub">'
                                   .$linfo['linkname'].'</a>'."<ul>\n<!--LEVEL-3--></ul>\n</li>\n";
                    }
                    else if($linfo['modulename'] == '' && $linfo['dynamicpara'] != ''){
                        if($linfo['disptitle'] == ''){ $linfo['disptitle'] = $linfo['linkname'];}
                        $dynamicmenu .= '<li><a href="'.$rtvdir.'/'.$linfo['dynamicpara'].'&tbl='.$tbl.'&tit='.$linfo['disptitle']
                            .'&db='.$linfo['thedb'].'&sid='.$sid.'&isheader=1&levelcode='.$linfo['levelcode'].'">'
                                    .$linfo['linkname'].'</a></li>'."\n";
                    }
					else{
						if($linfo['disptitle'] == ''){ $linfo['disptitle'] = $linfo['linkname'];}
                        $dynamicmenu .= '<li><a href="'.$ido.'&tbl='.$linfo['modulename'].'&tit='.$linfo['disptitle']
                            .'&db='.$linfo['thedb'].'&'.$linfo['dynamicpara'].'&levelcode='.$linfo['levelcode'].'">'
							.$linfo['linkname'].'</a></li>'."\n";
                    }
                    
					$lv3 = '';
                    foreach($hmkeysbylen[6] as $k2=>$v2){
                        #$lv3 .= "\tlevel3-v2:".$v2.", v1:$v1, \n";
                        if(strpos($v2, $v1) === 0){
                            $linfo = $hmkeys[$v2];
                            #$lv3 .= "\tlevel3-v2:".$v2.", v1:$v1, \n";
                            if($linfo['modulename'] == '' && $linfo['dynamicpara'] == ''){
                                $lv3 .= '<li><a href="javascript:void(0);" class="sub">'.$linfo['linkname'].'</a>'
                                        ."<ul>\n<!--LEVEL-4--></ul>\n</li>\n";
                            }
                            else if($linfo['modulename'] == '' && $linfo['dynamicpara'] != ''){
                                if($linfo['disptitle'] == ''){ $linfo['disptitle'] = $linfo['linkname'];}
                                $lv3 .= '<li><a href="'.$rtvdir.'/'.$linfo['dynamicpara'].'&tbl='.$tbl.'&tit='.$linfo['disptitle']
                                    .'&db='.$linfo['thedb'].'&sid='.$sid.'&isheader=1&levelcode='.$linfo['levelcode'].'">'
                                            .$linfo['linkname'].'</a></li>'."\n";
                            }
							else{
								if($linfo['disptitle'] == ''){ $linfo['disptitle'] = $linfo['linkname'];}
                                $lv3 .= '<li><a href="'.$ido.'&tbl='.$linfo['modulename'].'&tit='.$linfo['disptitle']
                                    .'&db='.$linfo['thedb'].'&'.$linfo['dynamicpara'].'&levelcode='.$linfo['levelcode'].'">'
									.$linfo['linkname'].'</a></li>'."\n";
                            }

							$lv4 = '';
                            foreach($hmkeysbylen[8] as $k3=>$v3){
								if(strpos($v3, $v2) === 0){
									$linfo = $hmkeys[$v3];
									#$lv4 .= "\t\tlevel4-v3:".$v3.", v2:$v2, v1:$v1,\n";
									if($linfo['disptitle'] == ''){ $linfo['disptitle'] = $linfo['linkname'];}
										$lv4 .= '<li><a href="'.$ido.'&tbl='.$linfo['modulename'].'&tit='
										        .$linfo['disptitle'].'&db='.$linfo['thedb']
										        .'&'.$linfo['dynamicpara'].'&levelcode='.$linfo['levelcode'].'">'
										        .$linfo['linkname'].'</a></li>';

								}
							}
							if($lv3 != ''){ $lv3 = str_replace("<!--LEVEL-4-->", $lv4, $lv3);  $lv4 = ''; }
                        }
                    }
					$dynamicmenu = str_replace("<!--LEVEL-3-->", $lv3, $dynamicmenu); $lv3 = '';
                }
            }

            $dynamicmenu .= "</ul>\n</li>\n";

            $lv3 = ''; $lv4 = '';
        }
    }
}

$menulist = '
	<ul class="menu" id="menu">
 	<li>
 
	<a href="'.$url.'" class="menulink"><img src="'.$rtvdir.'/img/my-desktop.png" alt="my desktop"  style="vertical-align:middle;height:12px" /> '.$lang->get('menu_desktop').'</a>
		<ul>
			<li><a href="'.$ido.'&tbl=fin_todotbl&tit='.$lang->get('menu_desktop_todo').'&db=&pnsktouser='.$userid.'&pnsm=1&pnskistate=1&pnsktogroup='
					.$user->getGroup().'&pnsc='.$pnscTodo.'&pnsck='.$pnsckTodo.'">'.$lang->get('menu_desktop_todo').'</a></li>
			<li><a href="'.$ido.'&tbl=fin_todotbl&tit='.$lang->get('menu_desktop_done').'&db=&pnsktouser='.$userid.'&pnsm=1&pnskistate=0&pnsktogroup='
					.$user->getGroup().'&pnsc='.$pnscDone.'&pnsck='.$pnsckDone.'&pnobid=1">'.$lang->get('menu_desktop_done').'</a></li>
			<li><a href="'.$ido.'&tbl=mynotetbl&tit='.$lang->get('menu_desktop_mynote').'&db=&pnskoperator='.$userid.'">'.$lang->get('menu_desktop_mynote').'</a></li>
			<li><a href="'.$ido.'&tbl=fin_operatelogtbl&tit='.$lang->get('menu_desktop_operatelog').'&db=&pnskuserid='.$userid.'">'.$lang->get('menu_desktop_operatelog').'</a></li>
			<li><a href="'.$ido.'&tbl=info_toolsettbl&tit='.$lang->get('menu_desktop_toolset').'">'.$lang->get('menu_desktop_toolset').'</a></li>
			<li><a href="'.$ido.'&tbl=filedirtbl&tit='.$lang->get('menu_desktop_filemgmt').'&pnskparentname=/&pnsm=1">'.$lang->get('menu_desktop_filemgmt').'</a></li>
			<li> <a href="'.$ido.'&tbl=info_objecttbl&tit='.$lang->get('menu_desktop_add2desktop').'">'.$lang->get('menu_desktop_add2desktop').'</a> </li>
		</ul>
 </li>
'.$dynamicmenu.'
	<li><a href="'.$url.'&navidir=99" orighref="javascript:void(0);" class="menulink">
		<img src="'.$rtvdir.'/img/my-setting.png" style="vertical-align:middle;height:16px" alt="Settings" /> '.$lang->get('menu_settings').'</a>
	 <ul>
		<li><a href="'.$ido.'&tbl=info_usertbl&tit=&db=">'.$lang->get('menu_setting_user').'</a></li>
		<li><a href="'.$ido.'&tbl=info_grouptbl&tit=&db=">'.$lang->get('menu_setting_usergroup').'</a></li>
		<li><a href="'.$ido.'&tbl=info_objecttbl&tit=&db=">'.$lang->get('menu_setting_module').'</a></li>
		<li><a href="'.$ido.'&tbl=info_objectgrouptbl&tit=&db=">'.$lang->get('menu_setting_modulegroup').'</a></li>
		<li><a href="'.$ido.'&tbl=useraccesstbl&tit=&db=">'.$lang->get('menu_setting_security').'</a></li>
		<li> <a href="'.$ido.'&tbl=info_menulist&tit=&db=">'.$lang->get('menu_setting_menuinfo').'</a> </li>
		<li><a href="javascript:void(0);" class="sub">'.$lang->get('menu_setting_guide').'</a>
			<ul>
			<li><a href="'.$ido.'&tbl=info_helptbl&pnskid=2&tit='.$lang->get('menu_setting_guide_companyinfo').'&db=&act=view&id=16">'.$lang->get('menu_setting_guide_companyinfo').'</a></li>
			<li><a href="'.$ido.'&tbl=info_helptbl&pnskisfaq=1&tit='.$lang->get('menu_setting_guide_faq').'&db=">'.$lang->get('menu_setting_guide_faq').'</a></li>
			<li><a href="'.$ido.'&tbl=info_helptbl&tit='.$lang->get('menu_setting_guide_topic').'&db=">'.$lang->get('menu_setting_guide_topic').'</a></li>
			</ul>
		</li>
		<li><a href="javascript:void(0);" class="sub">'.$lang->get('menu_setting_insitesearch').'</a>
			<ul>
			<li><a href="'.$ido.'&tbl=insitesearchtbl&tit='.$lang->get('menu_setting_searchconfig').'&db=">'.$lang->get('menu_setting_searchconfig').'</a></li>
			<li><a href="'.$ido.'&tbl=issblackwhitetbl&tit='.$lang->get('menu_setting_searchwblist').'&db=">'.$lang->get('menu_setting_searchwblist').'</a></li>
			</ul>
		</li>
	 </ul>
 	</li>
</ul>
 ';

$menulistjs = '
 <script async type="text/javascript">
	var menu = {};
	function initNaviMenu(){ //- will be exec in async mode
		menu = new parent.NaviMenu.dd("menu");
		menu.init("menu","menuhover");
	}
	if(typeof parent.NaviMenu == "undefined"){
		var menuDelayT=window.setTimeout(function(){ initNaviMenu();}, 2*1000);
	}
	else{
		initNaviMenu();
	}
 </script>
 ';

$menulist .= $menulistjs;

?>
