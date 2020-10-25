<?php

require 'shop.conn.php';

$to = get_var('to', '');

switch ($to) {
	case 'username':
		
		page_header($service['username']);

		$buy_points = $shop_config['buy_username'];

		$result = verify_points($buy_points);

		$template->assign_vars(array(
			'L_TITLE' => $service['username'],
			'MESSAGE' => $result['message'],
			'U_BUYING'	=> append_sid('loading.php?mod=shop&load=username'))
		);

		break;
	case 'namecolor':

		page_header($service['namecolor']);

		$buy_points = $shop_config['buy_namecolor'];

		$result = verify_points($buy_points);
		
		$template->assign_vars(array(
			'L_TITLE' => $service['namecolor'],
			'MESSAGE' => $result['message'],
			'U_BUYING'	=> append_sid('loading.php?mod=shop&load=namecolor'))
		);
		break;
	case 'rank':

		page_header($service['rank']);

		$buy_points = $shop_config['buy_rank'];

		$result = verify_points($buy_points);
		
		$template->assign_vars(array(
			'L_TITLE' => $service['rank'],
			'MESSAGE' => $result['message'],
			'U_BUYING'	=> append_sid('loading.php?mod=shop&load=rank'))
		);
		break;
	case 'qq':
		page_header($service['qq']);

		$buy_points = 0;

		$result = verify_points($buy_points);

		$template->assign_vars(array(
			'L_TITLE' => $service['qq'],
			'MESSAGE' => $result['message'],
			'U_BUYING'	=> append_sid('loading.php?mod=shop&load=qq'))
		);	
		break;
	case 'ad':
		page_header($service['ad']);

		$buy_points = 0;

		$result = verify_points($buy_points);

		$template->assign_vars(array(
			'L_TITLE' => $service['ad'],
			'MESSAGE' => $result['message'],
			'U_BUYING'	=> append_sid('loading.php?mod=shop&load=ad'))
		);

		break;
	case 'good':
		page_header($service['good']);

		$buy_points = 0;

		$result = verify_points($buy_points);

		$template->assign_vars(array(
			'L_TITLE' => $service['good'],
			'MESSAGE' => $result['message'],
			'U_BUYING'	=> append_sid('loading.php?mod=shop&load=good'))
		);		
		break;
	default:
		trigger_error('虚拟商店没有提供这样的服务');
		break;
}


if ($result['return'])
{
	$template->assign_block_vars('next', array());
}

$template->set_filenames(array(
	'body' => 'buy_body.tpl')
);

$template->assign_var('U_BACK', append_sid('loading.php?mod=shop'));

$template->pparse('body');

page_footer();

?>