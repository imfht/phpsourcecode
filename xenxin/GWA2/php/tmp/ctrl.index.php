<?php

#session_start();

# controlling of homepage
include_once($appdir."/ctrl/include/language.php");
include_once($appdir."/mod/poll.class.php");

#
$poll = new Poll();
$optiontbl = $_CONFIG['tblpre'].'polloptiontbl';
$votetbl = $_CONFIG['tblpre'].'pollvotetbl';

# actions

$act = $act == '' ? 'index' : $act;
$pollid = $_REQUEST['pollid'];

if($act == 'index'){
	#
	 
}
else if($act == 'create'){
	$theform = '<fieldset><legend>创建一个开放式投票</legend>
		<form id="createform-01" method="post" action="'.$url.'&act=create-do">
		<input name="ititle" value="标题"/>
		<br/><input name="idesc" value="描述" />
		<br/><input name="creator" value="创建人" />
		<br/><input name="ipassword" value="访问密码" />
		<br/><input type="submit" />
		</form></fieldset>';	
	$data['resp'] = $theform;
}
else if($act == 'create-do'){
	$poll->set('ititle', $_REQUEST['ititle']);
	$poll->set('idesc', $_REQUEST['idesc']);
	$poll->set('creator', $_REQUEST['creator']);
	$poll->set('ipassword', $_REQUEST['ipassword']);

	$hm = $poll->setBy('ititle, idesc, creator, ipassword,inserttime', null);
	$pollid = 0;
	if($hm[0]){
		$pollid = $hm[1]['insertid'];	
	}
	if($pollid > 0){
		$resp = '<a href="'.$url.'&act=init-option&pollid='.$pollid.'">Gooood! 成功！PollId:['.$pollid.'] 请牢记! 继续增加初始选项</a>';
	}
	else{
		error_log(__FILE__.": insert failed.");	
	}
	$data['resp'] = $resp;
}
else if($act == 'init-option'){
	$poll->setId($_REQUEST['pollid']);
	$hminfo = $poll->getBy("*", '');
	if($hminfo[0]){
		$hminfo = $hminfo[1][0];	
	}
	$theform = '<fieldset><legend>增加初始选项</legend>
		<br/>
		['.$hminfo['id'].'] '.$hminfo['ititle'].'
		<br/>
		'.$hminfo['idesc'].'
		<br/>
		By '.$hminfo['creator'].' @ '.$hminfo['inserttime'].'
		<form id="theform-02" method="post" action="'.$url.'&act=init-option-do">
		<textarea name="ioption" rows="10" cols="60">增加选项1（每行一个)'."\n".'选项2'."\n".'选项3</textarea>
		<br/><input type="hidden" name="creator" value="'.$hminfo['creator'].'" />
		<br/><input type="hidden" name="pollid" value="'.$hminfo['id'].'" />
		<br/><input type="submit" />
		</form></fieldset>';	
	$data['resp'] = $theform;
	
}
else if($act == 'init-option-do'){
	$maintbl = $poll->getTbl();
	$poll->setTbl($optiontbl);
	$poll->set('pollid', $_REQUEST['pollid']);
	$poll->set('creator', $_REQUEST['creator']);
	$options = $_REQUEST['ioption'];
	$tmpArr = explode("\n", $options);
	error_log(__FILE__.": options:[$options]");
	#print_r($tmpArr);
	foreach($tmpArr as $k=>$v){
		$poll->set('ioption', $v);
		#print "ioption:[$v] k:$k\n";
		$hmtmp = $poll->setBy('pollid, ioption, creator,inserttime', null);
		if($hmtmp[0]){
			#print_r($hmtmp[1]);	
			error_log(__FILE__.": add option succ.");
		}
		sleep(1);
	}
	$resp = '<a href="'.$url.'&act=retrieve-do&pollid='.$pollid.'">Gooood！成功！ 现在开始投票!</a>';
	$data['resp'] = $resp;

}
else if($act == 'retrieve'){
	$theform = '<fieldset><legend>取回/打开一个开放式投票</legend>
		<form id="createform-01" method="post" action="'.$url.'&act=retrieve-do">
		<input name="pollid" value="PollId"/>
		<br/><input name="ipassword" value="访问密码(可选)" />
		<br/><input type="submit" />
		</form></fieldset>';	
	$data['resp'] = $theform;

}
else if($act == 'retrieve-do'){
	$poll->setId($pollid);
	$hminfo = $poll->getBy("*", '');
	if($hminfo[0]){
		$hminfo = $hminfo[1][0];	
	}
	$option = new Poll();
	$option->setTbl($optiontbl);
	$option->set('pollid', $pollid);
	$option->set('orderby','id desc');
	$hmoption = $option->getBy('*', 'pollid=?');
	if($hmoption[0]){
		$hmoption = $hmoption[1];	
	}
	$vote = new Poll();
	$vote->setTbl($votetbl);
	$vote->set('pollid', $pollid);
	$hmvote = $vote->getBy('*', 'pollid=?');
	if($hmvote[0]){
		$hmvote = $hmvote[1];	
	}

	$theform = '<fieldset><legend>进行投票</legend>
		<br/>
		['.$hminfo['id'].'] '.$hminfo['ititle'].'
		<br/>
		'.$hminfo['idesc'].'
		<br/>
		By '.$hminfo['creator'].' @ '.$hminfo['inserttime'].'
		<form id="theform-02" method="post" action="'.$url.'&act=vote-do">
		<table name="thetable-01" style="padding:10px; line-height:22px">
		';
	$tmptbl = '';
	$sumArr = array();
	$tmptbl .= '<td>投票人</td>';
	foreach($hmoption as $k1=>$v1){
		$tmptbl .= '<td>'.$v1['ioption'].'</td>';	
	}
	$tmptbl .= '</tr><tr><td colspan="100"><hr/></td></tr>';

	foreach($hmvote as $k=>$v){
		$iname = $v['iname'];	
		$sumArr['iname']++;
		$ioption = $v['ioption']; # format "optionid1:optionvalue1,optionid2:value2,...."
		$arr1 = explode(",", $ioption);
		$optArr = array();
		foreach($arr1 as $k0=>$v0){
			$tmpArr = explode(":", $v0);
			$optArr[$tmpArr[0]] = $tmpArr[1];
		}
		$tmptbl .= "<tr><td>$iname</td>";	
		foreach($hmoption as $k1=>$v1){
			$tmptbl .= "<td>".($optArr[$v1['id']]==1?'Yes':'')."</td>";	
			if(!array_key_exists($v1['id'], $sumArr)){ $sumArr[$v1['id']] = 0; }
			if($optArr[$v1['id']]==1){ $sumArr[$v1['id']]++; }
		}
		$tmptbl .= '</tr>'; 

	}

	$voteid = $_REQUEST['voteid'];
	if($voteid == ''){
		$tmptbl .= '<tr><td><input name="iname" value="姓名" size="10" title="投票人姓名一定要填写" />
			<input type="hidden" name="pollid" value="'.$pollid.'" />
			<br/><input type="submit" /></td>';
		foreach($hmoption as $k1=>$v1){
			$tmptbl .= '<td><input type="checkbox" name="chk_'.$v1['id'].'[]"  title="勾选此项或不勾选通过下面的输入框输入新的选项" />
						'.$v1['ioption'].'
							<br/><input name="cho_'.$v1['id'].'" value="另填选项" size="10" /></td>';	
		}
		$tmptbl .= '</tr>'; 
	}
	if($voteid != ''){
		$tmptbl .= ' <tr><td colspan="100"><hr/></td></tr><tr><td>记票数</td>';
		foreach($hmoption as $k=>$v){
			$tmptbl .= "<td>".$v['ioption']."</td>";
		}	
		$tmptbl .= "</tr>";
		$tmptbl .= ' <tr><td colspan="100"><hr/></td></tr><tr><td>'.$sumArr['iname'].'</td>';
		foreach($hmoption as $k=>$v){
			$tmptbl .= "<td title='".sprintf("%.2f", ($sumArr[$v['id']]/$sumArr['iname'])*100)."%'>".$sumArr[$v['id']]."</td>";
		}	
		$tmptbl .= "</tr><tr><td colspan='100'><a href='".$url."&act=retrieve-do&pollid=".$pollid."&voteid=$voteid'>更新并刷新</a></td></tr>";
	}
	$theform .= $tmptbl.'</table></form></fieldset>';	
	$data['resp'] = $theform;

}
else if($act == 'vote-do'){
	$iname = $_REQUEST['iname'];
	$iticket = $_REQUEST['iticket'];
	if($iticket == ''){ $iticket = microtime(); }
	$option = new Poll();
	$option->setTbl($optiontbl);
	$option->set('pollid', $pollid);
	$hmoption = $option->getBy('*', 'pollid=?');
	if($hmoption[0]){
		$hmoption = $hmoption[1];	
	}
	$vote = new Poll();
	$vote->setTbl($votetbl);
	$vote->set('pollid', $pollid);
	
	$opt = '';
	foreach($hmoption as $k=>$v){
		$chk = $_REQUEST['chk_'.$v['id']];
		$cho = $_REQUEST['cho_'.$v['id']];
		if($chk != ''){
			#print ", checked!!!";
			$opt .= $v['id'].":1,";
		}
		else if($cho != '' && $cho != '另填选项'){
			#print "input new!!!";	
			$option->set('ioption', $cho);
			$option->set('creator', $iname);
			$hmtmp = $option->setBy('pollid,ioption,creator,inserttime', '');
			$optNewId = '';
			if($hmtmp[0]){
				$hmtmp = $hmtmp[1];
				$optNewId = $hmtmp['insertid'];
			}
			$opt .= $optNewId.":1,";
		}
		else{
			#print "no input!!!";	
		}
	}
	if(endsWith($opt,",")){ $opt = substr($opt, 0, strlen($opt)-1); }
	
	$vote->set('iname', $iname);
	$vote->set('ioption', $opt);
	$vote->set('iticket', $iticket);
	$hmtmp = $vote->setBy('pollid,iname,ioption,inserttime,iticket', '');
	$voteid = '';
	if($hmtmp[0]){
		$hmtmp = $hmtmp[1];
		$voteid = $hmtmp['insertid'];
	}
	else{
		error_log(__FILE__.": vote failed.");	
	}
	
	$data['resp'] = '<a href="'.$url.'&act=retrieve-do&pollid='.$pollid.'&voteid='.$voteid.'">Gooooood! 投票成功！ 继续查看结果...</a>';

}
else{
    $data['resp'] = "Unknown act:[$act].";
    
}

$data['time'] = date("H:i", time());


# tpl

if($out == '' && $smttpl == ''){ # if other module do not define a smttpl and $conf['display_style_smttpl']? 
	     
	$smttpl = 'homepage.html';
}

#print "unicode:[".utf8_decode($_REQUEST['u8s'])."]";
#var_dump($_REQUEST['u8s']);
#var_dump(utf8_decode($_REQUEST['u8s']));

?>
