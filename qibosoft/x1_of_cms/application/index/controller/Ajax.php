<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: CaiWeiMing
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\controller\IndexBase;
use app\admin\model\Attachment as AttachmentModel;


/**
 * 用于处理ajax请求的控制器
 * @package app\admin\controller
 */
class Ajax extends IndexBase
{
    
    /**
     * 检查附件是否存在
     * @param string $md5 文件md5
     * @return \think\response\Json
     */
    public function check($md5 = '')
    {
        $md5 == '' && $this->error('参数错误');
        
        // 判断附件是否已存在
        if ($file_exists = AttachmentModel::get(['md5' => $md5])) {
            if ($file_exists['driver'] == 'local') {
                $file_path = PUBLIC_URL.$file_exists['path'];
            } else {
                $file_path = $file_exists['path'];
            }
            return json([
                    'code'   => 1,
                    'info'   => '上传成功',
                    'class'  => 'success',
                    'id'     => $file_exists['path'],//$file_exists['id'],
                    'path'   => $file_path
            ]);
        } else {
            //$this->error('文件不存在');
            return $this->err_js('文件不存在!',[],0);   //这里要用0是因为Attachment.php中的成功返回代码还是1,暂时还没有批量处理
        }
    }
    
    /**
     * 登录接口参数
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function get_token(){
        if ($this->request->isAjax()) {
            if (empty($this->user)) {
                return $this->err_js('你还没登录!');  
            }else{
                $token = md5( $this->user['uid'] . $this->user['lastip']  . $this->user['lastvist'] );
                $user = $this->user;
                cache($token,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",60);
                return $this->ok_js($token);
            }            
        }else{
            return $this->err_js('无效访问!');
        }        
    }
    
    /**
     * webapp获取用户登录信息
     * @param string $pwd
     * @param string $fun
     * @return string
     */
    public function js_token($pwd='',$fun='get_userinfo')
    {
        $str = '';
        if (!empty($this->user)) {
            $token = md5( $this->user['uid'] . $this->user['lastip']  . $this->user['lastvist'] );
            $user = $this->user;
            cache($token,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",1800);
            $array = [
                'token'=>$token,
                'uid'=>$this->user['uid'],
                'username'=>$this->user['username'],
                'nickname'=>$this->user['nickname'],
                'money'=>$this->user['money'],
                'rmb'=>$this->user['rmb'],
                'icon'=>$this->user['icon']?get_url(tempdir($this->user['icon'])):'',
                'groupid'=>$this->user['groupid'],
                'groupname'=>getGroupByid($this->user['groupid']),
            ];
            $str = json_encode($array,JSON_UNESCAPED_UNICODE);
        }        
        return "{$fun}($str);";
    }
    
}