<?php
require __DIR__ . '/lib/G.class.php';
G::$configs = include(__DIR__ . '/config.php');

G::app_init();

function checkAuthorize(){
	$authorize = G::$configs['adminer']['authorize'];

	$oval = G::val($_GET, $authorize['id'], '');
	if ($oval !== $authorize['val'])
	{
		G::diestr('authorize failed!');
	}
}

function adminer_url($q, $params=array(), $anchor=''){

	$params = (array) $params;
	
	$params['q'] = trim($q);

	$authorize = G::$configs['adminer']['authorize'];
	$params[$authorize['id']] = G::val($_GET, $authorize['id'], '');

	$url = 'adminer.php?' . http_build_query($params);
	if (!empty($anchor))
	{
		$url .= '#' . trim($anchor);
	}

	return $url;
}

function app_init(){
	checkAuthorize();
	G::$tpl->assign('sitetitle', G::$configs['title']);
	G::$tpl->assign('tplPath', G::$configs['template_dir'] . '/adminer');
}

function app_index(){
	G::$tpl->assign('error', '');
	// 待执行(显示最新的20条),执行中(显示20条),执行失败(显示20条),已完成(显示20条)
	// 过滤到未启用的部署分支

	$repositorys = Sql::assistant( G::$ds )->select('gitman_repository', array());

	$repositoryIds	= array();
	foreach ($repositorys as $repository)
	{
		$repositoryIds[] = $repository['id'];
	}

	$deploys = Sql::assistant( G::$ds )->select('gitman_deploy', array(
					'xstatus' => GitWebhook::XSTATUS_ENABLE,
			));

	$deployIds = array();
	foreach ($deploys as $deploy)
	{
		$deployIds[] = $deploy['id'];
	}

	if (empty($repositoryIds)) $repositoryIds = [-1];
	if (empty($deployIds)) $deployIds = [-1];

	$nostarts = Sql::assistant( G::$ds )->select('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_NO,
					'repository_id'	=> array( $repositoryIds ),
					'deploy_id'	=> array( $deployIds ),
			), '*', 'created_at DESC', 20, true);
	G::$tpl->assign('nostarts', $nostarts);

	$executings = Sql::assistant( G::$ds )->select('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_START,
					'repository_id'	=> array( $repositoryIds ),
					'deploy_id'	=> array( $deployIds ),
			), '*', 'created_at DESC', 20, true);
	G::$tpl->assign('executings', $executings);

	$ends = Sql::assistant( G::$ds )->select('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_END,
					'repository_id'	=> array( $repositoryIds ),
					'deploy_id'	=> array( $deployIds ),
			), '*', 'created_at DESC', 20, true);
	G::$tpl->assign('ends', $ends);

	$faileds = Sql::assistant( G::$ds )->select('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_FAILED,
					'repository_id'	=> array( $repositoryIds ),
					'deploy_id'	=> array( $deployIds ),
			), '*', 'created_at DESC', 20, true);
	G::$tpl->assign('faileds', $faileds);


	$ignores = Sql::assistant( G::$ds )->select('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_IGNORE,
					'repository_id'	=> array( $repositoryIds ),
					'deploy_id'	=> array( $deployIds ),
			), '*', 'created_at DESC', 20, true);
	G::$tpl->assign('ignores', $ignores);

	
	$invalids = Sql::assistant( G::$ds )->select('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_INVALID,
					'repository_id'	=> array( $repositoryIds ),
					'deploy_id'	=> array( $deployIds ),
			), '*', 'created_at DESC', 20, true);
	G::$tpl->assign('invalids', $invalids);

	G::$tpl->assign('repositorys', Arrays::hashmap($repositorys, 'id') );
	G::$tpl->assign('deploys', Arrays::hashmap($deploys, 'id') );

	G::$tpl->display('adminer/dashboard.php');
}

function app_repository_list(){
	G::$tpl->assign('error', '');
	$repositorys = Sql::assistant( G::$ds )->select('gitman_repository', array());
	G::$tpl->assign('rows', $repositorys);

	G::$tpl->display('adminer/repository/list.php');
}

function app_repository_new(){
	G::$tpl->assign('error', '');
	G::$tpl->assign('repository', array());
	G::$tpl->display('adminer/repository/edit.php');
}

function app_repository_edit(){
	G::$tpl->assign('error', '');

	$id = G::val($_REQUEST, 'id', 0);
	if ($id)
	{
		$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'id' => $id,
			));

		if (!empty($repository))
		{
			G::$tpl->assign('repository', $repository);

			$deploys = Sql::assistant( G::$ds )->select('gitman_deploy', array(
					'repository_id' => $repository['id']
			));

			G::$tpl->assign('deploys', $deploys);
			return G::$tpl->display('adminer/repository/edit.php');
		}
		
	}

	G::diestr("无效id: " . $id);
}

function app_repository_save(){
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");

	$repository = (array) G::val($_REQUEST, 'repository', array());
	$repository['created_at'] = time();

	$rules = array(
		'name'	=> array(
			array('not_empty', '名称不能为空'),
		),
		'url'	=> array(
			array('not_empty', 'url不能为空'),
			array('is_url', 'url格式不正确'),
		),
		'platform'	=> array(
			array('not_null', 'platform不能为空'),
			array('is_int', 'platform必须是整数'),
		),
	);

	if (empty($repository['id']))
	{
		// do add
		if (Form::assert_arr($repository, $rules, $failed))
		{
			Sql::assistant( G::$ds )->insert('gitman_repository', $repository);
			G::redirect(adminer_url('repository.list'));
		}
		else
		{
			$error = current($failed);
		}

	}
	else
	{
		// do edit
		if (Form::assert_arr($repository, $rules, $failed))
		{
			Sql::assistant( G::$ds )->update('gitman_repository', $repository, array(
				'id'	=> $repository['id'],
			));
			G::redirect(adminer_url('repository.list'));
		}
		else
		{
			$error = current($failed);
		}
	}

	G::$tpl->assign('repository', $repository);
	G::$tpl->assign('error', $error);
	G::$tpl->display('adminer/repository/edit.php');
}

function app_repository_deploy_new(){
	G::$tpl->assign('error', '');

	$repository_id = G::val($_REQUEST, 'repository_id', 0);
	if ($repository_id)
	{
		$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'id' => $repository_id,
			));
		G::$tpl->assign('repository', $repository);
		G::$tpl->assign('deploy', array());

		return G::$tpl->display('adminer/repository/deploy/edit.php');
	}

	G::diestr("无效repository_id: " . $repository_id);
}

function app_repository_deploy_edit(){
	G::$tpl->assign('error', '');

	$id = G::val($_REQUEST, 'id', 0);
	if ($id)
	{
		$deploy = Sql::assistant( G::$ds )->select_row('gitman_deploy', array(
					'id' => $id,
			));

		if (!empty($deploy))
		{
			G::$tpl->assign('deploy', $deploy);

			$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'id' => $deploy['repository_id']
			));

			G::$tpl->assign('repository', $repository);

			$hookrecords = array('total'=>0,'rows'=>array());
			if (GitWebhook::REPO_PLATFORM_SELF == $repository['platform']){
				$hookrecords = Sql::assistant( G::$ds )->select('gitman_hookrecord', array(
						'repository_id'	=> $deploy['repository_id'],
						'deploy_id'	=> $deploy['id'],
				), '*', 'created_at DESC', 20, true);
			}

			G::$tpl->assign('hookrecords', $hookrecords);

			return G::$tpl->display('adminer/repository/deploy/edit.php');
		}
		
	}

	G::diestr("无效id: " . $id);
}

function app_repository_deploy_save(){
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");

	$deploy = (array) G::val($_REQUEST, 'deploy', array());
	$deploy['created_at'] = time();

	$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'id' => $deploy['repository_id']
			));

	if (empty($repository))
	{
		G::diestr("无效repository_id: " . $deploy['repository_id']);
	}

	$rules = array(
		'name'	=> array(
			array('not_empty', '名称不能为空'),
		),
		'webhook_branch_ref'	=> array(
			array('not_empty', 'webhook_branch_ref 不能为空'),
		),
		'branch_origin'	=> array(
			array('not_empty', 'branch_origin 不能为空'),
		),
		'code_dir'	=> array(
			array('not_empty', 'code_dir 不能为空'),
		),
		'mode'	=> array(
			array('not_null', 'mode 不能为空'),
			array('is_int', 'mode 必须是整数'),
		),
		'xstatus'	=> array(
			array('not_null', 'xstatus 不能为空'),
			array('is_int', 'xstatus 必须是整数'),
		),
	);

	if (empty($deploy['id']))
	{
		// do add
		if (Form::assert_arr($deploy, $rules, $failed))
		{
			Sql::assistant( G::$ds )->insert('gitman_deploy', $deploy);
			G::redirect(adminer_url('repository.edit',array('id' => $deploy['repository_id'])));
		}
		else
		{
			$error = current($failed);
		}
	}
	else
	{
		// do edit
		if (Form::assert_arr($deploy, $rules, $failed))
		{
			Sql::assistant( G::$ds )->update('gitman_deploy', $deploy, array(
				'id'	=> $deploy['id'],
			));
			G::redirect(adminer_url('repository.edit',array('id' => $deploy['repository_id'])));
		}
		else
		{
			$error = current($failed);
		}
	}	

	G::$tpl->assign('deploy', $deploy);
	G::$tpl->assign('repository', $repository);
	G::$tpl->assign('error', $error);
	G::$tpl->display('adminer/repository/edit.php');
}

function app_repository_hookrecord_self_new(){
	G::$tpl->assign('error', '');

	$repository_id = G::val($_REQUEST, 'repository_id', 0);
	$deploy_id = G::val($_REQUEST, 'deploy_id', 0);

	$deploy = Sql::assistant( G::$ds )->select_row('gitman_deploy', array(
					'repository_id' => $repository_id,
					'id' => $deploy_id,
					'xstatus' => GitWebhook::XSTATUS_ENABLE,
			));

	if (!empty($deploy))
	{
		$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'id' => $repository_id,
			));
		G::$tpl->assign('repository', $repository);
		G::$tpl->assign('deploy', $deploy);
		G::$tpl->assign('hookrecord', array());

		return G::$tpl->display('adminer/repository/hookrecord/self/edit.php');
	}

	G::diestr("无效参数!");
}

function app_repository_hookrecord_self_save(){
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");

	$hookrecord = (array) G::val($_REQUEST, 'hookrecord', array());

	if (empty($hookrecord['deploy_id']))
	{
		G::diestr("无效deploy_id");
	}

	$deploy = Sql::assistant( G::$ds )->select_row('gitman_deploy', array(
					'id' => $hookrecord['deploy_id']
			));

	if (empty($deploy))
	{
		G::diestr("无效deploy_id: " . $hookrecord['deploy_id']);
	}

	$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'id' => $hookrecord['repository_id'],
			));
	if (empty($repository))
	{
		G::diestr("无效repository_id: " . $hookrecord['repository_id']);
	}

	$rules = array(
		'commit_id'	=> array(
			array('not_empty', 'commit_id不能为空'),
		),
		'commit_author'	=> array(
			array('not_empty', 'commit_author 不能为空'),
		),
		'commit_msg'	=> array(
			array('not_empty', 'commit_msg 不能为空'),
		),
		'commit_date'	=> array(
			array('not_empty', 'commit_date 不能为空'),
			array('is_datetime', 'commit_date 必须是 datetime 格式'),
		),
	);

	if (empty($hookrecord['id']))
	{
		// do add
		if (Form::assert_arr($hookrecord, $rules, $failed))
		{
			$git_data = json_encode(array(
				'commits'	=> array(
					array(
						'id'		=> $hookrecord['commit_id'],
						'author'	=> $hookrecord['commit_author'],
						'message'	=> $hookrecord['commit_msg'],
						'timestamp'	=> strtotime($hookrecord['commit_date']),
					),
				),
			));

			$hhrow = array(
				'created_at'	=> time(),
				'deploy_id'	=> $hookrecord['deploy_id'],
				'repository_id' => $hookrecord['repository_id'],
				'do_status'	=> GitWebhook::DO_STATUS_NO,
				'mode'	=> $deploy['mode'],
				'do_at'	=> 0,
				'do_msg'	=> 'push by self',
				'commits_info'	=> $git_data,
				'webhook_type'	=> GitWebhook::WEBHOOK_TYPE_PUSH,
				'webhook_name'	=> 'push_hooks',
			);

			Sql::assistant( G::$ds )->insert('gitman_hookrecord', $hhrow);
			G::redirect(adminer_url('repository.deploy.edit',array('id' => $hookrecord['deploy_id'])));
		}
		else
		{
			$error = current($failed);
		}
	}

	G::$tpl->assign('deploy', $deploy);
	G::$tpl->assign('repository', $repository);
	G::$tpl->assign('hookrecord', $hookrecord);
	G::$tpl->assign('error', $error);
	G::$tpl->display('adminer/repository/hookrecord/self/edit.php');	
}

// #### remote call api ####

function app_remote_dealone(){

	$deploy_ids = G::val($_REQUEST, 'deploy_ids', '');
	$deploy_ids = G::normalize($deploy_ids, '|');

	$rs = array(
			'code'	=> 0,
			'data'		=> array(),
		);

	try {
		
		if (empty($deploy_ids))	 throw new Exception('参数 deploy_ids 无效!');

		$record = Sql::assistant( G::$ds )->select_row('gitman_hookrecord', array(
					'do_status' => GitWebhook::DO_STATUS_NO,
					'mode' => GitWebhook::DEPLOY_MODE_REMOTE,
					'deploy_id' => array( $deploy_ids ),

			), '*', 'created_at DESC');

		if (!empty($record)) {

			$deploy = Sql::assistant( G::$ds )->select_row('gitman_deploy', array(
					'id' => $record['deploy_id'],
			));

			if (empty($deploy)) {
				remote_hookrecord_invalidHandler($record, '无效 deploy: ' . $record['deploy_id']);
			}

			if ($deploy['xstatus'] != GitWebhook::XSTATUS_ENABLE) {
				remote_hookrecord_invalidHandler($record, 'deploy 被禁用: ' . $record['deploy_id']);
			}

			$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
							'id' => $record['repository_id'],
					));
			if (empty($repository)) {
				remote_hookrecord_invalidHandler($record, '无效 repository: ' . $record['repository_id']);
			}

			// 聚合出数据来返还给客户端

			if (empty($deploy['code_dir']))
			{
				remote_hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_FAILED, "deploy:{$record['deploy_id']}:code_dir is null", true);
			}

			if (empty($deploy['branch_origin']))
			{
				remote_hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_FAILED, "deploy:{$record['deploy_id']}:branch_origin is null", true);
			}
			
			$rs['data'] = array(

				'record_id'	=> $record['id'],
				'record_created_at'	=> $record['created_at'],
				'deploy_id'	=> $deploy['id'],
				'deploy_name'	=> $deploy['name'],
				'branch_origin'	=> $deploy['branch_origin'],
				'code_dir'	=> $deploy['code_dir'],
				'extra_commands'	=> $deploy['extra_commands'],

				'request_time'	=> $_SERVER['REQUEST_TIME'],
				
			);

			// 是否 将record状态标注成 已下发(执行中)
			$upstatus = G::val($_REQUEST, 'upstatus', 1);
			if ($upstatus == 1) {
				remote_hookrecord_doStatusHandler($record['id'], GitWebhook::DO_STATUS_START, 'start ..', false);
			}

		}

	}
	catch(Exception $ex){
		$rs = array(
			'code'	=> 1,
			'msg'		=> $ex->getMessage(),
		);
	}

	G::diestr(json_encode($rs));
}

function app_remote_report(){

	// r客户端 执行完成之后上报其执行结果
	if (!G::is_post()){
		G::diestr('http method must post!');
	}

	$record_id = G::val($_POST, 'record_id', 0);
	$do_status = G::val($_POST, 'do_status', 0);
	$do_msg = G::val($_POST, 'do_msg', '');

	$rs = array(
			'code'	=> 0,
			'data'		=> array(),
		);

	try {

		$record = Sql::assistant( G::$ds )->select_row('gitman_hookrecord', array('id' => $record_id));

		if (empty($record))	 throw new Exception('参数 record_id 无效!');

		// 将上报过来的数据更新到记录中
		remote_hookrecord_doStatusHandler($record['id'], $do_status, $do_msg, false);

		// 对其之前未执行的 hookrecod 做忽略处理
		Sql::assistant( G::$ds )->update('gitman_hookrecord', array(
				'do_status' => GitWebhook::DO_STATUS_IGNORE,
				'do_at' => time(),
				'do_msg' => "IGNORE by {$record['id']}/" . SqlHelper::timestamp($record['created_at']),
			), array(
				'do_status' => GitWebhook::DO_STATUS_NO,
				'deploy_id'	=> $record['deploy_id'],
				'repository_id'	=> $record['repository_id'],
				// 不包括自身的
				'id'	=> array($record['id'], '!='),
				// 创建时间小于当前记录的
				'created_at'	=> array($record['created_at'], '<='),			
			));
	}	
	catch(Exception $ex){
		$rs = array(
			'code'	=> 1,
			'msg'		=> $ex->getMessage(),
		);
	}

	G::diestr(json_encode($rs));
}


function remote_hookrecord_invalidHandler(array $record, $errorMsg='invalid'){

	// 将此分支对应的这一批未开始的记录都设置成无效
	Sql::assistant( G::$ds )->update('gitman_hookrecord', array(
			'do_status' => GitWebhook::DO_STATUS_INVALID,
			'do_at' => time(),
			'do_msg' => $errorMsg,
		), array(
			'do_status' => GitWebhook::DO_STATUS_NO,
			'deploy_id'	=> $record['deploy_id'],
			'repository_id'	=> $record['repository_id'],
		));

	throw new Exception("record: {$record['id']} invalid {$errorMsg}");
}

function remote_hookrecord_doStatusHandler($id, $do_status, $do_msg, $haderr=false){
	Sql::assistant( G::$ds )->update('gitman_hookrecord', array(
			'do_status' => $do_status,
			'do_at' => time(),
			'do_msg' => $do_msg,
		), array(
			'id'	=> $id,
		));

	if ($haderr) {
		throw new Exception("record: {$id} invalid {$do_msg}");
	}
}
