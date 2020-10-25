<?php
namespace Addons\Favorites\Controller;

use Home\Controller\AddonsController;
use Think\Hook;

class FavoritesController extends AddonsController{
    
    /*
         收藏插件
     */

    public function doFavorites()
    {
        if (!is_login()) {
            exit(json_encode(array('status' => 0, 'info' => '登陆后才可以收藏。')));
        }
        $appname = I('POST.appname');
        $table = I('POST.table');
        $row = I('POST.row');
        $aJump = I('POST.jump');
        $afield = I('POST.field');//收藏应用内容的字段

        $message_uid = intval(I('POST.uid'));
        $favorites['appname'] = $appname;
        $favorites['table'] = $table;
        $favorites['row'] = $row;
        $favorites['uid'] = is_login();

        if (D('Favorites')->where($favorites)->count()) {

            exit(json_encode(array('status' => 0, 'info' => '亲~你已经收藏过了，莫非你忘了，呼呼。')));
        } else {
            $favorites['create_time'] = time();
            if (D('Favorites')->where($favorites)->add($favorites)) {

                D($table)->where(array('id' => $row))->setInc($afield);  //向应用字段中写入收藏数
                
                $this->clearCache($favorites);
                
                $user = query_user(array('nickname'),get_uid());

                D('Message')->sendMessage($message_uid,$title = $user['nickname'] . '收藏了您。', $user['nickname'] . '收藏了亲的内容。',  $aJump , array('id' => $row));
                exit(json_encode(array('status' => 1, 'info' => '感谢您的收藏。')));
            } else {
                exit(json_encode(array('status' => 0, 'info' => '写入数据库失败。')));
            }

        }
    }

    /**
     * @param $favorites
     * @auth dameng
     */
    private function clearCache($favorites)
    {
        D('Favorites')->clearCache($favorites['appname'], $favorites['table'], $favorites['row']);
    }
}

