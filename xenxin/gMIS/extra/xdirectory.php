<?php
# directory management module
# wadelau@ufqi.com on Sun Jan 31 10:22:15 CST 2016
# updts by Xenxin@ufqi, Tue Apr 23 13:35:30 HKT 2019

#$isoput = false;

$isheader = 0;
$_REQUEST['isheader'] = $isheader;
#$out_header = $isheader;

require("../comm/header.inc.php");

include("../comm/tblconf.php");
include_once($appdir."/class/xdirectory.class.php");

if(!isset($xdirectory)){
	$xdirectory = new XDirectory($tbl);
}

$inframe = $_REQUEST['inframe'];

if($inframe == ''){
	# re open in an iframe window
	$myurl = $rtvdir."/extra/xdirectory.php?inframe=1&".$_SERVER['QUERY_STRING'].'&'.SID.'='.$sid;
    $out .= "<iframe id=\"linktblframe\" name=\"linktblframe\" width=\"100%\" height=\"100%\" src=\""
            .$myurl."&isheader=0\" frameborder=\"0\"></iframe>";

}
else{

$dirLevelLength = 2;

# main actions

$out .= '
	<script type="text/javascript" src="'.$rtvdir.'/comm/jquery-3.4.1.min.js" charset="utf-8"></script>
	<style type="text/css">
		.node ul{
			margin-left:-25px;
		}
		.node ul li{
			list-style-type:none;
		}
		.node .node{
			display:none;
		}
		.node .tree{
			height:24px;
			line-height:24px;
		}
		.ce_ceng_close{
			background:url(../img/cd_zd1.png) left center no-repeat;
			padding-left: 15px;
		}
		.ce_ceng_open{
			background:url(../img/cd_zd.png) left center no-repeat;
		}
	</style>
	';
		
$out .= "";
$icode = $_REQUEST['icode'];
$iname = $_REQUEST['iname'];

$parentCode = Wht::get($_REQUEST, 'parentcode');
if(inString('-', $parentCode)){ //- 0100-止疼药2级
    $tmpArr = explode('-', $parentCode);
    $parentCode = $tmpArr[0];
}
$xdirectory->set('parentCode', $parentCode);

$expandList = array();
if(strlen($parentCode) > $dirLevelLength){
	$codeV = '';
	$codeArr = str_split(substr($parentCode,0,strlen($parentCode)-$dirLevelLength), $dirLevelLength);
	foreach($codeArr as $k=>$v){
		$codeV .= $v;
		$expandList[$codeV] = $codeV;
	}
}
#debug("extra/xdir: parentcode:$parentCode expandList:".serialize($expandList));

$list = array();
$sqlCondi = "1=1 order by $icode asc";
$hm = $xdirectory->getBy("$icode, $iname", "$sqlCondi");
if($hm[0]){
	$hm = $hm[1];
}
else{
	$hm = array(0=>array("$icode"=>'00', "$iname"=>'所有/All'));
}
#debug($hm);
if(1){
	foreach($hm as $k=>$v){
		if($v[$icode] == ''){
			$v[$icode] = '00'; # init hierarchy
		}
		$list[$v[$icode]] = $v[$iname];
	}
	$out .= '<style type="text/css">';
	foreach($list as $k=>$v){
		$out .= '#nodelink'.$k.'{ width:168px; height:20px; display:none; }';
	}
	$out .= '</style>';
	$str = $xdirectory->getList($list, $dirLevelLength);
	$out .= $str;
}

$targetField = Wht::get($_REQUEST, 'targetfield');
$imode = Wht::get($_REQUEST, "imode");
if($imode == 'read' && $targetField != $icode){
    $icode = $targetField;
}

$out .= " <script type=\"text/javascript\"> var current_link_field='".$icode
    ."'; var tmpTimer0859=window.setTimeout(function(){parent.sendLinkInfo('".$parentCode."','w', current_link_field);}, 1*1000);</script> ";

$out .= '
		<script type="text/javascript">
			$(".tree").each(function(index, element) {
				if($(this).next(".node").length>0){
					$(this).addClass("ce_ceng_close");
				}
				else{
					$(this).css("padding-left","15px");
				}
			});
		';
		
foreach($expandList as $k=>$v){
	$out .= '
		var ull = $("#'.$v.'").next(".node");
		ull.slideDown();
		$("#'.$v.'").addClass("ce_ceng_open");
		ull.find(".ce_ceng_close").removeClass("ce_ceng_open");
		';
}
								
$out .= '
		$(".tree").click(function(e){
			var ul = $(this).next(".node");
			if(ul.css("display")=="none"){
				ul.slideDown();
				$(this).addClass("ce_ceng_open");
				ul.find(".ce_ceng_close").removeClass("ce_ceng_open");
			}else{
				ul.slideUp();
				$(this).removeClass("ce_ceng_open");
				ul.find(".node").slideUp();
				ul.find(".ce_ceng_close").removeClass("ce_ceng_open");
			}
		});
		';

$out .= '
    //- disp menu options    
    function xianShi(nodeId) {
			document.getElementById("nodelink"+nodeId).style.display="inline";
        }
    //- hide options
	function yinCang(nodeId) {
			document.getElementById("nodelink"+nodeId).style.display="none";
        }
    //- highlights selected
    var lastSelectedK = \'\';
    function changeBgc(nodeId){
        var myObj = document.getElementById(nodeId);
        if(myObj){
            myObj.style.backgroundColor=\'silver\';
        }
        if(lastSelectedK != \'\' && lastSelectedK != nodeId){
            myObj = document.getElementById(lastSelectedK);
            if(myObj){
                myObj.style.backgroundColor=\'\';
            }
        }
        lastSelectedK = nodeId;
    }
    ';
	
# positioning to selected, 17:42 6/11/2020
if($parentCode != ''){
	$out .= 'if(true){ var tmpReloadTimer=window.setTimeout(function(){ var tmpObj=document.getElementById("'.$parentCode.'"); if(tmpObj){tmpObj.scrollIntoView(); parent.scrollTo(0,10);}}, 1*1000);};';
}

$out .='</script>';

}

require("../comm/footer.inc.php");

?>