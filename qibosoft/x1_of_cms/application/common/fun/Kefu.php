<?php
namespace app\common\fun;

class Kefu{
    
    /**
     * 客服初始化信息
     * @param string $kefu 客服UID多个用逗号隔开
     * @return string
     */
    public function init($kefu=''){
        if (empty($kefu)) {
            $kefu = config('webdb.weixin_reply_kefu')?:1;
        }
        $array = explode(',',trim(str_replace(['，',' ','　'],',',$kefu),"　, "));
        $code = '';
        foreach ($array AS $key=>$kid){
            $user = get_user($kid);
            if (empty($user)) {
                unset($array[$key]);
                continue;
            }
            $code .= "\r\nKF.kefu_list[{$kid}] = ".json_encode([
                'name'=>$user['nickname']?:$user['username'],
                'icon'=>tempdir($user['icon']),
                'sign'=>$user['introduce'],                
            ], JSON_UNESCAPED_UNICODE);
        }
        $userdb = login_user();
        $kefu_id = $array[array_rand($array)];
        $my_uid = $userdb?$userdb['uid']:0;
        $ws_url = fun('Gatewayclient@client_url');
        $userinfo = json_encode($userdb?fun('member@format',$userdb):['uid'=>0]);
        $jsfile = '';
        if ( is_file(STATIC_PATH.'layui/lay/modules/layim.js') ) {
            if(IN_WAP===true){
                $jsfile = '<link rel="stylesheet" href="'.STATIC_URL.'layui/css/layui.mobile.css">
                            <script type="text/javascript">
                            if(typeof(layui)=="undefined"){
                            	document.write(\'<script LANGUAGE="JavaScript" src="'.STATIC_URL.'layui/layui.js"><\/script>\');
                            }
                            </script>
                            <script LANGUAGE="JavaScript" src="'.STATIC_URL.'layui/wap_kefu.js"></script>';
            }else{
                $jsfile = '<link rel="stylesheet" href="'.STATIC_URL.'layui/css/layui.css">
                            <script type="text/javascript">
                            if(typeof(layui)=="undefined"){
                            	document.write(\'<script LANGUAGE="JavaScript" src="'.STATIC_URL.'layui/layui.js"><\/script>\');
                            }
                            </script>
                            <script LANGUAGE="JavaScript" src="'.STATIC_URL.'layui/kefu.js"></script>';
            }
        }
            
        return "<script type=\"text/javascript\">
                 $code
                  WS.link({
                    	kefu:{$kefu_id}
                    	,kefu_info:KF.kefu_list[{$kefu_id}]
                    	,userinfo:$userinfo
                    	,my_uid:{$my_uid}
                    	,ws_url:\"{$ws_url}\",
                  });
                  </script>
                  {$jsfile}";
    }

}