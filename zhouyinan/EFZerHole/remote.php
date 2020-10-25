<?php
require_once('./config.php');
require_once('./functions.php');
if(REMOTE_ENABLED != true){header("http/1.1 403 Forbidden");exit('Remote access is disabled by config.php');}
if(empty($_REQUEST['message'])){header("http/1.1 400 Bad Request");exit('Field message is required.');}
if(REMOTE_SIGNATURE_REQUIRED === true){
  if(empty($_REQUEST['sig'])){header("http/1.1 401 Unauthorized");exit('Access denied because a valid signature is required.');}
  if($_REQUEST['sig'] != md5($_REQUEST['message'] . REMOTE_SIGNATURE_KEY)){header("http/1.1 403 Forbidden");exit('Access denied because of the invalid signature');}
}
if(REMOTE_SOURCE_CUSTOM_DISPLAY_ENABLED === true && !empty($_REQUEST['source'])){$source = removeXSS($_REQUEST['source']);}else{$source = REMOTE_SOURCE_DEFAULT_DISPLAY;}
$message = removeXSS($_REQUEST['message']);
$id = GenerateMessageID();
$content = GenerateContent($source,$message);
if(MESSAGE_PUSH === true){PushMessage($content);}
if(MESSAGE_STORE_ENABLED === true){AddMessageToKVDB($id,$content);}
header("http/1.1 204 No Content");
if(RENREN_PUBLISH_ENABLED === true){RenRenStatusUpdate($message,$id);}