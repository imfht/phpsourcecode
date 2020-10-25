<?php
require_once('common.php');
if(empty($_GET['user']) || empty($_GET['project']) || empty($_GET['type'])){
    header('Location: https://gitee.com/hamm/svg_badge_tool');
    die;
}
$user = trim($_GET['user'] ?? 'hamm');
$project = trim($_GET['project'] ?? 'svg_badge_tool');
$type = trim($_GET['type'] ?? 'star');
$key = 'Gitee';
$value = '';
$url = "https://gitee.com/api/v5/repos/".$user."/".$project;
$result = httpGetFull($url);
$giteeArray = json_decode($result,true);
switch($type){
    case 'star':
        if(array_key_exists('message',$giteeArray)){
            $value = "? Stars";
        }else{
            $value = $giteeArray["stargazers_count"] . ' Stars';
        }
        break;
    case 'fork':
        if(array_key_exists('message',$giteeArray)){
            $value = "? Forks";
        }else{
            $value = $giteeArray["forks_count"] . ' Forks';
        }
        break;
    case 'watch':
        if(array_key_exists('message',$giteeArray)){
            $value = "? Watches";
        }else{
            $value = $giteeArray["watchers_count"] . ' Watches';
        }
        break;
    case 'commit':
        $url = "https://gitee.com/".$user."/".$project;
        $urlForSvg = "https://gitee.com/".$user."/".$project."/commits/master";
        try{
            $html = httpGetFull($url);
            if(preg_match('/icon-commit\'><\/i>\n<b>(.*?)<\/b>/', $html, $matches)){
                $value = $matches[1]." Commits";
            }else{
                $value = "? Commits";
            }
        }catch(Exception $e){
            $value = "? Commits";
        }
        break;
    default:
}
require_once('svg.php');
?>
