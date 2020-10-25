<?php
class inputClassModel extends Model
{
	public function initModel()
	{
		$this->settable('flow_element');
	}
	
	
	public function getsubtable($modeid,$iszb=1, $hang=1, $ism=0, &$zbshu=1)
	{
		if($iszb<=0)$iszb=1;
		if($hang<=0)$hang=1;
		
		$rows 	= $this->getall("`mid`='$modeid' and `iszb`=$iszb and `islu`=1",'`isbt`,`fields`,`fieldstype`,`name`','`sort`');
		if(!$rows)return '';
		$xu	 = $iszb-1;
		$str = '<table class="tablesub" id="tablesub'.$xu.'" style="width:100%;" border="0" cellspacing="0" cellpadding="0">';
		$str.='<tr>';
		$str.='<td width="10%" nowrap>序号</td>';
		$strm= $str;
		$yfsr= array('hidden','fixed');
		$yczd= array();
		$zlen= 0;
		foreach($rows as $k=>$rs){
			$zlen++;
			$xh = '';
			if($rs['isbt']==1)$xh='*';
			if(in_array($rs['fieldstype'], $yfsr)){
				$yczd[] = $rs['fields'];
				continue;
			}
			$str.='<td nowrap>'.$xh.''.$rs['name'].'</td>';
		}
		$strm .= '<td>{subzbname'.$xu.'}</td></tr><tr><td>[xuhao'.$xu.',0]</td>';
		$zbshu = $zlen;
		$str.='<td width="5%" nowrap>操作</td>';
		$str.='</tr>';
		$strm1= '';
		for($j=0;$j<$hang;$j++){
			$str.='<tr>';
			$str.='<td>[xuhao'.$xu.','.$j.']</td>';
			$yoi =0;
			$tstr= '';
			foreach($rows as $k=>$rs){
				if(in_array($rs['fieldstype'], $yfsr))continue;
				$tstr='['.$rs['fields'].''.$xu.','.$j.']';
				if(isset($rows[$k+1]) && in_array($rows[$k+1]['fieldstype'], $yfsr)){
					$tstr.='['.$rows[$k+1]['fields'].''.$xu.','.$j.']'; //隐藏字段
				}
				$xh = '';
				if($rs['isbt']==1)$xh='*';
				$strm1.='<div align="left"><font color=#888888>'.$xh.''.$rs['name'].'：</font></div><div style="border-bottom:1px #aaaaaa solid" align="left">'.$tstr.'</div>';
				$str .='<td>'.$tstr.'</td>';
				$yoi++;
			}
			$str.='<td>{删,'.$xu.'}</td>';
			$str.='</tr>';
		}
		$str.='</table>';
		$strm.= '<td>'.$strm1.'{删,'.$xu.'}</td></tr></table>';
		if($ism==0)$str.='<div style="background-color:#F1F1F1;">{新增,'.$xu.'}</div>';
		if($ism==1){
			$str.='<div>{新增,'.$xu.'}</div>';
			$strm.='<div>{新增,'.$xu.'}</div>';
			//return $strm;//如果你想移动端录入页显示一起就返回这个
		}
		return $str;
	}
}