<?php
namespace app\index\controller;
use wechatenterprise\webhook\WechatRobot; 
use \think\Log;
use think\Request;
class Index extends  \think\Controller
{ 
    public function index()
    { 
		
		$X_Gitee_Token = Request::instance()->header('X-Gitee-Token');
		$X_Gitee_Event = Request::instance()->header('X-Gitee-Event');
		if($X_Gitee_Token=="qq530566495")
		{ 
			$json_content =file_get_contents("php://input");
			Log::ERROR($json_content);    
			$gitWebHookBean = json_decode($json_content,true);
			switch($X_Gitee_Event)
			{
				case "Note Hook":
					$actionName = $gitWebHookBean['action'];
					$project = $gitWebHookBean['project'];
					$msgTpl = "";
					 
					$comment = $gitWebHookBean['comment']; 
					$msgTpl =  $comment['user']['name'];
					switch($gitWebHookBean['noteable_type'])
					{ 
						case "Comment": $msgTpl .=" 对项目做了评论\n"; break;//项目评论
						case "Issue":$msgTpl .=" 对Issue 【".$comment['title']."】 做了评论\n"; break;//Issue评论
						case "PullRequest":$msgTpl .=" 对PullRequest 做了评论 \n";  break;//PullRequest评论
					}
					$msgTpl .= $comment['body'] ."【".$project['full_name'] ."】";  
					break;
				case "Push Hook": 
					$project = $gitWebHookBean['project'];
					$msgTpl = ""; 
					$commits = $gitWebHookBean['commits']; 
					$msgTpl = $gitWebHookBean['pusher']['name'];
					$msgTpl .=" 推送 【".$gitWebHookBean['ref'] ."】 \n";  
					$msgTpl .= $gitWebHookBean['head_commit']['message'] ."【".$project['full_name'] ."】";  
					break; 
				case "Tag Push Hook": 
					$project = $gitWebHookBean['project'];
					$msgTpl = ""; 
					$commits = $gitWebHookBean['commits']; 
					$msgTpl = $gitWebHookBean['pusher']['name'];
					$msgTpl .=" 新建标签 【".$gitWebHookBean['ref'] ."】 \n";  
					$msgTpl .= $gitWebHookBean['head_commit']['message'] ."【".$project['full_name'] ."】";  
					break; 	
				default:
					$msgTpl = "未知：".$X_Gitee_Event; 
				break;	  
			}
			

			$resultJson = WechatRobot::SendMsg($msgTpl); 
			echo json_encode($resultJson);
		}else
		{
			echo "token error";
		}
		
		exit();
    } 
	public function flushtoken()
	{
		$token = input("token");
		if(isset($token))
		{
			cache('wechat_entper_access_token',null); 
			//重新访问
		}
		echo "Excute Success";
		exit();
	}
}
