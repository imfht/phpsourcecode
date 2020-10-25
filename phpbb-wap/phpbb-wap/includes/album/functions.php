<?php
/******************************************
 *		album_functions.php
 *		-------------------
 *   	Разработка: (C) 2003 Smartor
 *   	Модификация: Гутник Игорь ( чел )
 ******************************************/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

function album_user_access($cat_id, $passed_auth = 0, $view_check, $upload_check, $rate_check, $comment_check, $edit_check, $delete_check)
{
	global $db, $album_config, $userdata;

	$moderator_check = 1;

	$album_user_access = array(
		'view' => 0,
		'upload' => 0,
		'rate' => 0,
		'comment' => 0,
		'edit' => 0,
		'delete' => 0,
		'moderator' => 0
	);
	$album_user_access_keys = array_keys($album_user_access);

	if ($cat_id == PERSONAL_GALLERY)
	{
		$personal_gallery_access = personal_gallery_access(1,1);

		if ($personal_gallery_access['view'])
		{
			$album_user_access['view'] = 1;
		}

		if ($personal_gallery_access['upload'])
		{
			$album_user_access['upload'] = 1;
			$album_user_access['rate'] = 1;
			$album_user_access['comment'] = 1;

			$album_user_access['edit'] = 1;
			$album_user_access['delete'] = 1;

			if ($userdata['session_logged_in'])
			{
				if ($userdata['user_level'] == ADMIN)
				{
					$album_user_access['moderator'] = 1;
				}
			}
		}

		return $album_user_access;
	}
	else if ($cat_id < 0)
	{
		message_die(GENERAL_ERROR, 'Bad cat_id arguments for function album_user_access()');
	}

	if ($userdata['user_level'] == ADMIN)
	{
		for ($i = 0; $i < count($album_user_access); $i++)
		{
			$album_user_access[$album_user_access_keys[$i]] = 1; // Authorised All
		}

		return $album_user_access;
	}

	if (!$userdata['session_logged_in'])
	{
		$edit_check = 0;
		$delete_check = 0;
		$moderator_check = 0;
	}

	if ($album_config['rate'] == 0)
	{
		$rate_check = 0;
	}
	if ($album_config['comment'] == 0)
	{
		$comment_check = 0;
	}

	$access_type = array();

	if ($view_check != 0)
	{
		$access_type[] = 'view';
	}

	if ($upload_check != 0)
	{
		$access_type[] = 'upload';
	}

	if ($rate_check != 0)
	{
		$access_type[] = 'rate';
	}

	if ($comment_check != 0)
	{
		$access_type[] = 'comment';
	}

	if ($edit_check != 0)
	{
		$access_type[] = 'edit';
	}

	if ($delete_check != 0)
	{
		$access_type[] = 'delete';
	}

	if( empty($access_type) and (!$moderator_check) )
	{
		return $album_user_access;
	}

	$sql = 'SELECT cat_id';

	for ($i = 0; $i < count($access_type); $i++)
	{
		$sql .= ', cat_'. $access_type[$i] .'_level, cat_'. $access_type[$i] .'_groups';
	}

	if ($moderator_check)
	{
		$sql .= ', cat_moderator_groups';
	}

	$sql .= "
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id = '$cat_id'";
	if( !is_array($passed_auth) )
	{
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not query Album Category information' ,'' , __LINE__, __FILE__, $sql);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = $passed_auth;
	}

	$groups_access = array();
	for ($i = 0; $i < count($access_type); $i++)
	{
		switch ($thiscat['cat_'. $access_type[$i] .'_level'])
		{
			case ALBUM_GUEST:
				$album_user_access[$access_type[$i]] = 1;
				break;

			case ALBUM_USER:
				if ($userdata['session_logged_in'])
				{
					$album_user_access[$access_type[$i]] = 1;
				}
				break;

			case ALBUM_PRIVATE:
				if( ($thiscat['cat_'. $access_type[$i] .'_groups'] != '') and ($userdata['session_logged_in']) )
				{
					$groups_access[] = $access_type[$i];
				}
				break;

			case ALBUM_MOD:
				break;

			case ALBUM_ADMIN:
				$album_user_access[$access_type[$i]] = 0;
				break;

			default:
				$album_user_access[$access_type[$i]] = 0;
		}
	}

	if( ($moderator_check == 1) and ($thiscat['cat_moderator_groups'] != '') )
	{
		$groups_access[] = 'moderator';
	}

	if (empty($groups_access))
	{
		return $album_user_access;
	}

	for ($i = 0; $i < count($groups_access); $i++)
	{
		$sql = "SELECT group_id, user_id
				FROM ". USER_GROUP_TABLE ."
				WHERE user_id = '". $userdata['user_id'] ."' AND user_pending = 0
					AND group_id IN (". $thiscat['cat_'. $groups_access[$i] .'_groups'] .")";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not query User-Group information' ,'' , __LINE__, __FILE__, $sql);
		}

		if( $db->sql_numrows($result) > 0 )
		{
			$album_user_access[$groups_access[$i]] = 1;
		}
	}

	if( ($album_user_access['moderator'] == 1) and ($moderator_check == 1) )
	{
		for ($i = 0; $i < count($album_user_access); $i++)
		{
			if( $thiscat['cat_'. $album_user_access_keys[$i] .'_level'] != ALBUM_ADMIN )
			{
				$album_user_access[$album_user_access_keys[$i]] = 1;
			}
		}
	}
	return $album_user_access;
}

function personal_gallery_access($check_view, $check_upload)
{
	global $db, $userdata, $album_config;

	$personal_gallery_access = array(
		'view' => 0,
		'upload' => 0,
	);

	if ($check_upload)
	{
		switch ($album_config['personal_gallery'])
		{
			case ALBUM_USER:
				if ($userdata['session_logged_in'])
				{
					$personal_gallery_access['upload'] = 1;
				}
				break;

			case ALBUM_PRIVATE:
				if( ($userdata['session_logged_in']) and ($userdata['user_level'] == ADMIN) )
				{
					$personal_gallery_access['upload'] = 1;
				}
				else if(!empty($album_config['personal_gallery_private']) and $userdata['session_logged_in'])
				{
					$sql = "SELECT group_id, user_id
							FROM ". USER_GROUP_TABLE ."
							WHERE user_id = '". $userdata['user_id'] ."' AND user_pending = 0
								AND group_id IN (". $album_config['personal_gallery_private'] .")";
					if( !$result = $db->sql_query($sql) )
					{
						message_die(GENERAL_ERROR, 'Could not query User-Group information' ,'' , __LINE__, __FILE__, $sql);
					}

					if( $db->sql_numrows($result) > 0 )
					{
						$personal_gallery_access['upload'] = 1;
					}
				}
				break;

			case ALBUM_ADMIN:
				if( ($userdata['session_logged_in']) and ($userdata['user_level'] == ADMIN) )
				{
					$personal_gallery_access['upload'] = 1;
				}
				break;
		}
	}

	if ($check_view)
	{
		switch ($album_config['personal_gallery_view'])
		{
			case ALBUM_GUEST:
				$personal_gallery_access['view'] = 1;
				break;

			case ALBUM_USER:
				if ($userdata['session_logged_in'])
				{
					$personal_gallery_access['view'] = 1;
				}
				break;

			case ALBUM_PRIVATE:
				if( ($userdata['session_logged_in']) and ($userdata['user_level'] == ADMIN) )
				{
					$personal_gallery_access['view'] = 1;
				}
				else if(!empty($album_config['personal_gallery_private']) and $userdata['session_logged_in'])
				{
					$sql = "SELECT group_id, user_id
							FROM ". USER_GROUP_TABLE ."
							WHERE user_id = '". $userdata['user_id'] ."' AND user_pending = 0
								AND group_id IN (". $album_config['personal_gallery_private'] .")";
					if( !$result = $db->sql_query($sql) )
					{
						message_die(GENERAL_ERROR, 'Could not query User-Group information' ,'' , __LINE__, __FILE__, $sql);
					}

					if( $db->sql_numrows($result) > 0 )
					{
						$personal_gallery_access['view'] = 1;
					}
				}
				break;
		}
	}

	return $personal_gallery_access;
}

function init_personal_gallery_cat($user_id = 0)
{
	global $userdata, $db, $album_config;

	if ($user_id == 0)
	{
		$user_id = $userdata['user_id'];
	}

	$sql = "SELECT COUNT(pic_id) AS count
			FROM ". ALBUM_TABLE ."
			WHERE pic_cat_id = ". PERSONAL_GALLERY ."
				AND pic_user_id = ". $user_id;

	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not count pics for this personal gallery', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrow($result);

	$count = $row['count'];

	if ($user_id != $userdata['user_id'])
	{
		$sql = "SELECT user_id, username
				FROM ". USERS_TABLE ."
				WHERE user_id = $user_id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain user information', '', __LINE__, __FILE__, $sql);
		}

		$user_row = $db->sql_fetchrow($result);
		$username = $user_row['username'];
	}
	else
	{
		$username = $userdata['username'];
	}

	$thiscat = array(
		'cat_id' => 0,
		'cat_title' => $username . '的个人相册',
		'cat_desc' => '',
		'cat_order' => 0,
		'count' => $count,
		'cat_view_level' => $album_config['personal_gallery_view'],
		'cat_upload_level' => $album_config['personal_gallery'],
		'cat_rate_level' => $album_config['personal_gallery_view'],
		'cat_comment_level' => $album_config['personal_gallery_view'],
		'cat_edit_level' => $album_config['personal_gallery'],
		'cat_delete_level' => $album_config['personal_gallery'],
		'cat_view_groups' => $album_config['personal_gallery_private'],
		'cat_upload_groups' => $album_config['personal_gallery_private'],
		'cat_rate_groups' => $album_config['personal_gallery_private'],
		'cat_comment_groups' => $album_config['personal_gallery_private'],
		'cat_edit_groups' => $album_config['personal_gallery_private'],
		'cat_delete_groups' => $album_config['personal_gallery_private'],
		'cat_delete_groups' => $album_config['personal_gallery_private'],
		'cat_moderator_groups' => '',
		'cat_approval' => 0
	);

	return $thiscat;
}

function album_end()
{
	global $album_config;

	echo '<div align="center" style="font-family: Verdana; font-size: 10px; letter-spacing: -1px">Powered by Photo Album Addon 2' . $album_config['album_version'] . ' &copy; 2002, 2003 <a href="http://smartor.is-root.com" target="_blank">Smartor</a></div>';
}

?>