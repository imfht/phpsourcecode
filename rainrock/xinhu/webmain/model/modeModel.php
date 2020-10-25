<?php
class modeClassModel extends Model
{
	public function initModel()
	{
		$this->settable('flow_set');
	}
	public function getmodearr($whe='')
	{
		$arr = $this->getall('status=1 '.$whe.'','`id`,`num`,`name`,`table`,`type`,`isflow`','sort');
		foreach($arr as $k=>$rs){
			$arr[$k]['name'] = ''.$rs['id'].'.'.$rs['name'].'('.$rs['num'].')';
		}
		return $arr;
	}
	
	//判断模块是否开启存在
	public function iscun($num)
	{
		$to = $this->rows("`num`='$num' and `status`=1");
		return $to==1;
	}
	
	public function getmoderows($uid, $sww='')
	{
		$where	= m('admin')->getjoinstr('receid', $uid);
		$arr 	= $this->getall("`status`=1 and `type`<>'系统' $sww $where",'`id`,`num`,`name`,`table`,`type`,`isflow`,`isscl`','`sort`');
		return $arr;
	}
	
	public function getmodemyarr($uid=0, $whe='')
	{
		$where = '';
		if($whe!='')$where = $whe;
		if($uid>0)$where = m('admin')->getjoinstr('receid', $uid);
		$arr = $this->getall('status=1 and isflow>0 '.$where.'','`id`,`name`,`type`','sort');
		return $arr;
	}
	
	//生成列表页面
	public function createlistpage($modeid, $lxss=0)
	{
		if(is_array($modeid)){
			$mors	= $modeid;
		}else{
			$mors 	= m('flow_set')->getone($modeid,'`id`,`table`,`names`,`num`,`name`,`isflow`,`lbztxs`');
		}
		$num	= $mors['num'];
		$path 	= ''.P.'/flow/page/rock_page_'.$num.'.php';
		
		//当是一键生成时，不存在就不要生成了
		if($lxss==1 && !file_exists($path))return 'none';
		
		$flow	= m('flow')->initflow($num);
		$chufarr= array();
		if(method_exists($flow, 'flowxiangfields'))$chufarr = $flow->flowxiangfields($chufarr);
		
		$table	= $mors['table'];
		$name	= $mors['name'];
		$modeid	= (int)$mors['id'];
		$isflow	= (int)$mors['isflow'];
		$lbztxs	= $mors['lbztxs'];
		$columnsstr = '';
		$showzt	= false;
		if($isflow>0){
			$columnsstr = '{text:"'.arrvalue($chufarr, 'base_name', '申请人').'",dataIndex:"base_name",sortable:true},{text:"'.arrvalue($chufarr, 'base_deptname', '申请人部门').'",dataIndex:"base_deptname",sortable:true},{text:"'.arrvalue($chufarr, 'base_sericnum', '单号').'",dataIndex:"sericnum"},';
		}
		
		$farr[] = array('name'=>arrvalue($chufarr, 'base_name', '申请人'),'fields'=>'base_name');
		$farr[] = array('name'=>arrvalue($chufarr, 'base_deptname', '申请人部门'),'fields'=>'base_deptname');
		$farr[] = array('name'=>arrvalue($chufarr, 'base_sericnum', '单号'),'fields'=>'sericnum');
		$farrs 	= m('flow_element')->getall("`mid`='$modeid' and `iszb`=0",'`fields`,`name`,`fieldstype`,`ispx`,`isalign`,`islb`','`sort`');
		foreach($farrs as $k=>$rs){
			$farr[] = $rs;
			if($rs['islb']==0)continue;
			$columnsstr.='{text:"'.$rs['name'].'",dataIndex:"'.$rs['fields'].'"';
			if($rs['ispx']==1)$columnsstr.=',sortable:true';
			if($rs['isalign']==1)$columnsstr.=',align:"left"';
			if($rs['isalign']==2)$columnsstr.=',align:"right"';
			$columnsstr.='},';
			if($rs['fields']=='status')$showzt=true;
		}
		if($isflow>0){
			$columnsstr.='{text:"状态",dataIndex:"statustext"},';
		}
		$jgpstr 	= '<!--SCRIPTend-->';
		$hstart 	= '<!--HTMLstart-->';
		$hendts 	= '<!--HTMLend-->';
		$oldcont 	= @file_get_contents($path);
		$autoquye	= $this->rock->getcai($oldcont,'//[自定义区域start]','//[自定义区域end]');
		
		$isdaoru 	= m('flow_element')->rows("`mid`='$modeid' and `isdr`=1");
		$drstrbtn	= '';
		if($isdaoru>0){
			$drstrbtn	= "<span style=\"display:none\" id=\"daoruspan_{rand}\"><button class=\"btn btn-default\" click=\"daoru,1\" type=\"button\">导入</button>&nbsp;&nbsp;&nbsp;</span>";
		}
		
		//读取流程模块的条件
		$whtml 		= '<div id="changatype{rand}" class="btn-group"></div>';
		$zthtml		= '';
		if($isflow>0)$showzt = true;
		if($lbztxs==1)$showzt = true;
		if($lbztxs==2)$showzt = false;
		if($showzt){
			$ztarr	= $flow->getstatusarr();
			$zthtml = '<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option>';
			foreach($ztarr as $zt=>$ztv){
				if($isflow==0 && $zt==23)continue;
				$zthtml .= '<option style="color:'.arrvalue($ztv, 1).'" value="'.$zt.'">'.$ztv[0].'</option>';
			}
			$zthtml .= '</select></td>';
			$zthtml	 = str_replace('?','', $zthtml);
		}
		$fselarr	= array();
		$bear		= $this->db->getrows('[Q]option',"`num` like 'columns_".$num."_%'",'`num`,`value`');
		foreach($bear as $k2=>$rs2)$fselarr[$rs2['num']]=$rs2['value'];
		$placeholder= '关键字';
		if($isflow>0)$placeholder= '关键字/申请人/单号';
		
$html= "".$hstart."
<div>
	<table width=\"100%\">
	<tr>
		<td style=\"padding-right:10px;\" id=\"tdleft_{rand}\" nowrap><button id=\"addbtn_{rand}\" class=\"btn btn-primary\" click=\"clickwin,0\" disabled type=\"button\"><i class=\"icon-plus\"></i> 新增</button></td>
		<td>
			<input class=\"form-control\" style=\"width:160px\" id=\"key_{rand}\" placeholder=\"".$placeholder."\">
		</td>
		$zthtml
		<td style=\"padding-left:10px\">
			<div style=\"white-space:nowrap\">
			<button style=\"border-right:0;border-top-right-radius:0;border-bottom-right-radius:0\" class=\"btn btn-default\" click=\"searchbtn\" type=\"button\">搜索</button><button class=\"btn btn-default\" id=\"downbtn_{rand}\" type=\"button\" style=\"padding-left:8px;padding-right:8px;border-top-left-radius:0;border-bottom-left-radius:0\"><i class=\"icon-angle-down\"></i></button> 
			</div>
		</td>
		<td  width=\"90%\" style=\"padding-left:10px\">$whtml</td>
	
		<td align=\"right\" id=\"tdright_{rand}\" nowrap>
			".$drstrbtn."<button class=\"btn btn-default\" style=\"display:none\" id=\"daobtn_{rand}\" disabled click=\"daochu\" type=\"button\">导出 <i class=\"icon-angle-down\"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class=\"blank10\"></div>
<div id=\"view".$num."_{rand}\"></div>
".$hendts."";		
$str = "<?php
/**
*	模块：".$num.".".$name."
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.".$name."]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = '".$num."',modename='".$name."',isflow=".$isflow.",modeid='".$modeid."',atype = params.atype,pnum=params.pnum,modenames='".$mors['names']."',listname='".$this->rock->jm->base64encode($table)."';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = ".json_encode($farr).",fieldsselarr= ".json_encode($fselarr).",chufarr= ".json_encode($chufarr).";
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

$autoquye

//[自定义区域end]

	js.initbtn(c);
	var a = $('#view'+modenum+'_{rand}').bootstable(bootparams);
	c.init();
	
});
</script>
".$jgpstr."";	
		$bstrs = '<!--HTML-->';
		if(!isempt($oldcont) && contain($oldcont, $jgpstr) && contain($oldcont, $bstrs)){
			$strarr = explode($jgpstr, $oldcont);
			$nstr 	= $strarr[1];
			$htmlqy = $this->rock->getcai($nstr, $hstart, $hendts);
$rstr 	= "".$hstart."
".$htmlqy."
".$hendts."";
			$nstr 	= str_replace($rstr, '', $nstr);
			$nstr 	= str_replace($bstrs, $html.$bstrs, $nstr);
			$str	.= "\n".$nstr;
		}else{
			$str.= "\n".$html;
		}
		$bo = $this->rock->createtxt($path, $str);
		if(!$bo)$path='';
		return $path;
	}
	
	
	
	
	
	
	
	
	
	
	
	/**
	*	统计
	*/
	public function flowtotal($modeid, $fields, $type)
	{
		
	}
}