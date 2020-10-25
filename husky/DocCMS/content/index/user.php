<?php
function doc_user_login($sid,$style=0)
{
	require(get_style_file('user','user_login',$style));
}

function doc_user_regist($sid,$style=0)
{
	require(get_style_file('user','user_register',$style));
}
?>