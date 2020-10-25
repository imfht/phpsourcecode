<?php
// 此文件 用于接收 gitee(码云) 推送过来的 数据

require __DIR__ . '/lib/G.class.php';

G::$configs = include(__DIR__ . '/config.php');

G::app_init();

function app_index(){

	if (!G::is_post()){
		G::diestr('http method must post!');
	}

	$git_data = file_get_contents('php://input');

	try {
		if (!empty($git_data)){
			$gitPushData = GitPushData::parse_str($git_data);

			if (empty($gitPushData->data['pusher'])){
				G::diestr('repository not a push data!');
			}

			$repository_url = $gitPushData->data['repository']['url'];
			$repository = Sql::assistant( G::$ds )->select_row('gitman_repository', array(
					'url' => $repository_url,
					'platform' => GitWebhook::REPO_PLATFORM_GITHUB,
			));

			if (empty($repository))
			{
				G::diestr('repository not set: ' . $repository_url);
			}

			$webhook_branch_ref = $gitPushData->data['ref'];
			
			$deploy = Sql::assistant( G::$ds )->select_row('gitman_deploy', array(
					'repository_id' => $repository['id'],
					'webhook_branch_ref' => $webhook_branch_ref,
			));

			if (empty($deploy))
			{
				G::diestr('deploy not set: ' . $webhook_branch_ref);
			}

			if (GitWebhook::XSTATUS_ENABLE !== intval($deploy['xstatus']))
			{
				G::diestr('deploy:branch not enable: ' . $deploy['name']);
			}

			$webhook_name = 'push_hooks';
			$webhook_type = GitWebhook::WEBHOOK_TYPE_PUSH;

			$hookrecord = array(
				'created_at'	=> time(),
				'deploy_id'	=> $deploy['id'],
				'repository_id' => $repository['id'],
				'mode'	=> $deploy['mode'],
				'do_status'	=> GitWebhook::DO_STATUS_NO,
				'do_at'	=> 0,
				'do_msg'	=> 'push by github',
				'commits_info'	=> $git_data,
				'webhook_type'	=> $webhook_type,
				'webhook_name'	=> $webhook_name,
			);
			
			$id = Sql::assistant( G::$ds )->insert('gitman_hookrecord', $hookrecord, true);
			var_dump($_GET);
			G::diestr('record success: ' . date('m-d-H-i-') . $id );
		}
		else
		{
			G::diestr('postdata is null');
		}
	}
	catch(Exception $ex){
		G::diestr($ex->getMessage());
	}
	
}
