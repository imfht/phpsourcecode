<?php

/**
 * @Title: ajaxReturn
 * @Description: todo(ajax提交返回状态信息)
 * @param string $info
 * @param url $url
 * @param string $status
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function ajaxReturn($info='', $url='', $status='', $data = ''){
    if(!empty($url)){   //操作成功
        $result = array( 'info' => '操作成功', 'status' => 1, 'url' => $url, );
    }else{   //操作失败
        $result = array( 'info' => '操作失败', 'status' => 0, 'url' => '', );
    }
    if(!empty($info)){$result['info'] = $info;}
    if(!empty($status)){$result['status'] = $status;}
    if(!empty($data)){$result['data'] = $data;}
    return json($result);
    exit();
}


