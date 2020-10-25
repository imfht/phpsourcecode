<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

$unhtml_specialchars_match 		= array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
$unhtml_specialchars_replace 	= array('>', '<', '"', '&');
$error 							= FALSE;
$error_msg 						= '';

if ( isset($_POST['submit']) )
{
	
	require(ROOT_PATH . 'includes/functions/validate.php');

	$strip_var_list = array(
		'qq' 			=> 'qq',
		'number' 		=> 'number',
		'aim' 			=> 'aim',
		'msn'	 		=> 'msn',
		'yim' 			=> 'yim',
		'website' 		=> 'website',
		'location' 		=> 'location',
		'occupation' 	=> 'occupation',
		'interests' 	=> 'interests',
		'signature' 	=> 'signature'
	);

	foreach($strip_var_list as $var => $param)
	{
		if ( !empty($_POST[$param]) )
		{
			$$var = trim(htmlspecialchars($_POST[$param]));
		}
	}

	$gender = ( isset($_POST['gender']) ) ? intval ($_POST['gender']) : 0;

	if ( isset($_POST['birthday']) )
	{
		$birthday = intval($_POST['birthday']);
		if ( $birthday != 999999 )
		{
			$b_day	= realdate('j',$birthday); 
			$b_md 	= realdate('n',$birthday); 
			$b_year	= realdate('Y',$birthday);
		}
	} 
	else
	{
		$b_day 		= ( isset($_POST['b_day']) ) ? intval ($_POST['b_day']) : 0;
		$b_md 		= ( isset($_POST['b_md']) ) ? intval ($_POST['b_md']) : 0;
		$b_year 	= ( isset($_POST['b_year']) ) ? intval ($_POST['b_year']) : 0;
		if ( $b_day && $b_md && $b_year )
		{
			$birthday = mkrealdate($b_day,$b_md,$b_year);
		}
		else
		{
			$birthday = 999999;
		}
	}

	validate_optional_fields($qq, $aim, $msn, $yim, $website, $location, $occupation, $interests, $signature);

	$user_id = intval($_POST['user_id']);
	
	if ( $user_id != $userdata['user_id'] )
	{
		$error 		= TRUE;
		$error_msg .= '<p>您不能编辑他人的资料</p>';
	}

	if ( $b_day || $b_md || $b_year ) 
	{
		$user_age = ( date('md') >= $b_md.(($b_day <= 9) ? '0':'').$b_day ) ? date('Y') - $b_year : date('Y') - $b_year - 1 ;
		
		if ( !checkdate($b_md,$b_day,$b_year) )
		{
			$error = TRUE;
			$error_msg .= '<p>生日格式无效</p>';
		}
		else if ( $user_age > $board_config['max_user_age'] )
		{
			$error = TRUE;
			$error_msg .= '<p>年龄不能大于' . $board_config['max_user_age'] . '岁</p>';
		} 
		else if ( $user_age < $board_config['min_user_age'] )
		{
			$error = TRUE;
			$error_msg .= '<p>年龄不能小于' . $board_config['min_user_age'] . '岁</p>';
		} 
		else
		{
			$birthday = ( $error ) ? $birthday : mkrealdate($b_day, $b_md, $b_year);
			$next_birthday_greeting = ( date('md') < $b_md . ( ($b_day <= 9) ? '0' : '' ) . $b_day ) ? date('Y') : date('Y') + 1;
		}
	}
	else
	{
		if ($board_config['birthday_required'])
		{
			$error = TRUE;
			$error_msg .= '<p>生日选项不能为空</p>';
		}
		$birthday = 999999;
		$next_birthday_greeting = 0;
	}

	if ( !$error )
	{
			$user_active = 1;
			$user_actkey = '';

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_qq = '" . str_replace("\'", "''", $qq) . "', user_number = '" . str_replace("\'", "''", $number) . "',user_website = '" . str_replace("\'", "''", $website) . "', user_occ = '" . str_replace("\'", "''", $occupation) . "', user_from = '" . str_replace("\'", "''", $location) . "', user_interests = '" . str_replace("\'", "''", $interests) . "', user_birthday = '$birthday', user_next_birthday_greeting = '$next_birthday_greeting', user_aim = '" . str_replace("\'", "''", $aim) . "', user_yim = '" . str_replace("\'", "''", $yim) . "', user_msnm = '" . str_replace("\'", "''", $msn) . "', user_gender = '$gender', user_sig = '" . str_replace("\'", "''", $signature) . "'
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not update users table', E_USER_WARNING);
			}

			$message = '个人资料修改成功<br />点击 <a href="' . append_sid('ucp.php?mode=editprofileinfo') . '">这里</a> 返回上一页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

			trigger_error($message);
	}
}


if ( $error )
{
	$qq 			= stripslashes($qq);
	$aim 			= stripslashes($aim);
	$msn 			= stripslashes($msn);
	$yim 			= stripslashes($yim);

	$website 		= stripslashes($website);
	$signature 		= stripslashes($signature);
	$location 		= stripslashes($location);
	$occupation 	= stripslashes($occupation);
	$interests 		= stripslashes($interests);
}
else
{
	$user_id 		= $userdata['user_id'];
	$qq 			= $userdata['user_qq'];
	$aim 			= $userdata['user_aim'];
	$msn 			= $userdata['user_msnm'];
	$yim 			= $userdata['user_yim'];

	$website 		= $userdata['user_website'];
	$signature 		= $userdata['user_sig'];
	$location 		= $userdata['user_from'];
	$occupation 	= $userdata['user_occ'];
	$interests 		= $userdata['user_interests'];
	$number 		= $userdata['user_number'];
    $gender			= $userdata['user_gender']; 
	$birthday 		= $userdata['user_birthday'];
}

page_header($page_title);

if ( $user_id != $userdata['user_id'] )
{
	$error 		= TRUE;
	$error_msg 	= '您不能编辑他人的资料';
}

require(ROOT_PATH . 'includes/functions/selects.php');

if ( !isset($coppa) )
{
	$coppa = FALSE;
}

$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="agreed" value="true" /><input type="hidden" name="coppa" value="' . $coppa . '" />';

$s_hidden_fields .= '<input type="hidden" name="user_id" value="' . $userdata['user_id'] . '" />';
$s_hidden_fields .= '<input type="hidden" name="current_email" value="' . $userdata['user_email'] . '" />';

$gender_male_checked = '';
$gender_female_checked = '';
$gender_no_specify_checked = '';
switch ($gender) 
{ 
	case 1:
		$gender_male_checked = 'checked="checked"';
	break; 
	case 2:
		$gender_female_checked = 'checked="checked"';
	break; 
	default:
		$gender_no_specify_checked = 'checked="checked"';
}

if ( $birthday != 999999 )
{
	$b_day = realdate('j', $birthday);
	$b_md = realdate('n', $birthday);
	$b_year = realdate('Y', $birthday);
	$birthday = realdate('Y-m-d', $birthday);
}
else
{
	$b_day = '';
	$b_md = '';
	$b_year = '';
	$birthday = '';
}

if ( $error )
{
	error_box('ERROR_BOX', $error_msg);
}

$template->set_filenames(array(
	'body' => 'ucp/edit_info.tpl')
);

$s_b_day = '<select name="b_day" size="1" class="gensmall"> 
	<option value="0">-</option> 
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
	<option value="31">31</option>
	</select>日';
$s_b_md = '<select name="b_md" size="1" class="gensmall"> 
		<option value="0">-</option> 
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	</select>月';
$s_b_day	= str_replace('value="' . $b_day . '">', 'value="' . $b_day . '" selected="selected">', $s_b_day);
$s_b_md 	= str_replace('value="'.$b_md.'">', 'value="'.$b_md.'" selected="selected">', $s_b_md);
$s_b_year 	= '<input type="text" name="b_year" size="4" maxlength="4" value="' . $b_year . '" />年'; 
$s_birthday	= $s_b_year . $s_b_md . $s_b_day;

$template->assign_vars(array(
	'YIM' 						=> $yim,
	'QQ' 						=> $qq,
	'MSN' 						=> $msn,
	'AIM' 						=> $aim,
	'OCCUPATION' 				=> $occupation,
	'INTERESTS' 				=> $interests,
	'NUMBER' 					=> $number,
	'S_BIRTHDAY' 				=> $s_birthday,
	'BIRTHDAY_REQUIRED'			=> ($board_config['birthday_required']) ? '<font color="red">(必填)</font>' : '',
	'LOCATION' 					=> $location,
	'WEBSITE' 					=> $website,
	'SIGNATURE' 				=> str_replace('<br />', "\n", $signature),
	'GENDER_NO_SPECIFY_CHECKED'	=> $gender_no_specify_checked, 
	'GENDER_MALE_CHECKED' 		=> $gender_male_checked, 
	'GENDER_FEMALE_CHECKED' 	=> $gender_female_checked, 

	'U_UCP'						=> append_sid('ucp.php?mode=viewprofile&amp;u=' . $userdata['user_id']),
	'S_HIDDEN_FIELDS' 			=> $s_hidden_fields,
	'S_PROFILE_ACTION' 			=> append_sid('ucp.php'))
);

$template->pparse('body');

page_footer();
?>