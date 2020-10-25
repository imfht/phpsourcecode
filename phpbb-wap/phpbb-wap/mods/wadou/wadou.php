<?php
/**
* @package phpBB-WAP
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/*
* MOD名称: 挖豆
* MOD支持地址: http://zisuw.com
* MOD描述: 资速网原挖豆插件
* MOD作者: 熊大
* MOD版本: v6.0
* MOD显示: on
*/

page_header('挖宝');

if (isset($_GET['act'])) {
	$act = $_GET['act'];
} else {
    $act = 'index';
} 

$rand = mt_rand(100, 999);

echo '<div class="title" >挖豆</div>';

if ( $userdata['session_logged_in'] )
{
    switch ($act):
    	case "index":

	        echo '<div class="row1"><img src="mods/wadou/images/1.png" alt="image" /></div>';
	        echo '<div class="row1">你有: ' . $userdata['user_points'] . $board_config['points_name'] . '</div>';
			echo '<div class="row1">失败会扣取50个' . $board_config['points_name'] . '.</div>';
	        echo '<div class="row1">成功可以获得得250个' . $board_config['points_name'] . '.</div>';
			echo '<a href="loading.php?mod=wadou&amp;act=choice">我要挖豆</a>';

        break;
    case "choice":

        if (isset($_SESSION['naperstki'])) {
            $_SESSION['naperstki'] = "";
            unset($_SESSION['naperstki']);
        } 

        echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=1&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> ';
        echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=2&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> ';
		echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=3&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> <br/>';
		echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=4&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> ';
        echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=5&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> ';
        echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=6&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> <br/>';
		echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=7&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> ';
        echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=8&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> ';
        echo '<a href="loading.php?mod=wadou&amp;act=go&amp;thimble=9&amp;rand=' . $rand . '"><img src="mods/wadou/images/2.png" alt="image" /></a> <br/>';

        echo '<div class="row1">请选择一块土地</div>';
		echo '<div class="row1">您目前有： ' .$userdata['user_points'] . $board_config['points_name'] . '</div>';
        echo '<div><a href="loading.php?mod=wadou&amp;">返回</a></div>';
        break;
    case "go":

        $thimble = (int)$_GET['thimble'];
        if (!isset($_SESSION['naperstki'])) {
            $_SESSION['naperstki'] = 0;
        } 

        if ($userdata['user_points'] >= 50) {
            if ($_SESSION['naperstki'] < 9) {
                $_SESSION['naperstki']++;

                $rand_thimble = mt_rand(1, 9);

                if ($rand_thimble == 1) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> ';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" /> ';
                } 
				if ($rand_thimble == 2) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> ';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" /> ';
                }	
				if ($rand_thimble == 3) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> <br/>';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" /> <br/>';
                } 
                if ($rand_thimble == 4) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> ';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" /> ';
                } 
                if ($rand_thimble == 5) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> ';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" />';
                } 
				if ($rand_thimble == 6) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> <br/>';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" /> <br/>';
                } 
				if ($rand_thimble == 7) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> ';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" /> ';
                } 
                if ($rand_thimble == 8) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> ';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" />';
                } 
				if ($rand_thimble == 9) {
                    echo '<img src="mods/wadou/images/3.png" alt="image" /> ';
                } else {
                    echo '<img src="mods/wadou/images/2.png" alt="image" /> <br/>';
                } 
                if ($thimble == $rand_thimble)
				{
                   
					$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points+500 WHERE user_id=".$userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('无法增加用的的金币', E_USER_WARNING);
				}
		
                    echo '<div class="row1">恭喜你，你挖到了豆豆，获得250红豆' . $board_config['points_name'] . '！</div>';
                }
                else 
				{
                   $sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points-50 WHERE user_id=".$userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('无法扣取用的的金币', E_USER_WARNING);
				}
                    echo '<div class="row1">唉，什么也没挖到，还不见了50豆</div>';
           } 
            } else {
			
				message_die(GENERAL_MESSEGE, '您必须选择一块土地');              
			} 

            echo '<div class="row1"><a href="loading.php?mod=wadou&amp;act=choice&amp;rand=' . $rand . '">继续玩</div>';
           
            echo '<div><a href="loading.php?mod=wadou&amp;">返回</a></div>';
        }
		else 
		{
			echo '你不可以挖，你没有足够的' . $board_config['points_name']; 
			echo '<div>- <a href="loading.php?mod=wadou&amp;">返回</a></div>';     
		} 
        break; 
    default:
       echo '- <a href="loading.php?mod=wadou&amp;">返回</a><br />';
        exit;
        endswitch;
} 
else 
{
	
	echo '<div class="row1"><img src="mods/wadou/images/1.png" alt="image" /></div>';
	echo '<div class="row">你还没有登录噢！<a href="' . login_back('loading.php?mod=wadou', true) . '">登录</a></div>';
} 
echo '<div class="nav"><a href="' . append_sid('mods.php') . '">返回上级</a> / <a href="' . append_sid('index.php') . '">返回首页</a></div>';

page_footer();
?>