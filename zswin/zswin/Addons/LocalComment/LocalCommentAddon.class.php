<?php

namespace Addons\LocalComment;

use Common\Controller\Addon;

/**
 * 本地评论插件
 * @author caipeichao
 */
class LocalCommentAddon extends Addon
{

    public $info = array(
        'name' => 'LocalComment',
        'title' => '本地评论',
        'description' => '本地评论插件，不依赖社会化评论平台',
        'status' => 1,
        'author' => 'caipeichao',
        'version' => '0.1'
    );

    public function install()
    {
        $prefix = C("DB_PREFIX");
        D()->execute("DROP TABLE IF EXISTS `{$prefix}local_comment`");
        D()->execute(<<<SQL
CREATE TABLE IF NOT EXISTS `{$prefix}local_comment` (
`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
`uid` int(11) NOT NULL,
`app` text NOT NULL,
`con` text NOT NULL,
`row_id` int NOT NULL,
`parse` int(11) NOT NULL DEFAULT '0',
`content` varchar(1000) NOT NULL,
`create_time` int(11) NOT NULL,
`pid` int(11) NOT NULL DEFAULT '0',
`ding` int(10) DEFAULT '0' COMMENT '支持',
`cai` int(10) DEFAULT '0' COMMENT '反对',
`status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
        );
        return true;
    }

    public function uninstall()
    {
        $prefix = C("DB_PREFIX");
        D()->execute("DROP TABLE IF EXISTS `{$prefix}local_comment`");
        return true;
    }

    //实现的documentDetailAfter钩子方法
    /**
     * @param 
     * @param $uid int 评论给谁？
     * @author caipeichao
     */
    public function localComment($param)
    {
       
        $uid = $param['uid'];
        $app = $param['app'];
        $mod = $param['con'];
        $row_id = $param['id'];
        //获取参数
        
        $p = $_REQUEST[C('VAR_PAGE')] ? $_REQUEST[C('VAR_PAGE')] : 1;
       
        $count = 7;

        //调用接口获取评论列表
        $list = $this->getCommentList($app, $mod, $row_id, $p, $count);
        $total_count = $this->getCommentCount($app, $mod, $row_id);

        //增加用户信息
        foreach ($list as &$e) {
            $e['user'] = query_user(array('uid', 'avatar64', 'nickname', 'space_url'),$e['uid']);
        }
        unset($e);
        $config=  get_addon_config('LocalComment');
        $can_guest_comment=$config['can_guest_comment'];
         $this->assign('can_guest_comment', $can_guest_comment);
        
        //显示页面
       $loginuser = query_user(array('uid', 'avatar64', 'nickname', 'space_url'),$uid);
       $this->assign('loginuser', $loginuser);
        $this->assign('list', $list);
        $this->assign('total_count', $total_count);
        $this->assign('count', $count);
        $this->assign('app', $app);
        
        $this->assign('mod', $mod);
        
        $info=D($app.'/'.$mod)->get_info($row_id);
        $this->assign('info', $info);
        $this->assign('row_id', $row_id);
        $this->assign('uid', $uid);
        
        $this->display('comment');
    }
    
   
    
    public function getCommentList($app, $mod, $row_id, $page, $count)
    {
        $model = $this->getCommentModel();
        $map = array('app' => $app, 'con' => $mod, 'row_id' => $row_id, 'status' => 1);
        $list = $model->where($map)->order('create_time desc')->page($page, $count)->select();
        
        
        
        
        return $list;
    }

    public function getCommentCount($app, $mod, $row_id)
    {
        $model = $this->getCommentModel();
        $map = array('app' => $app, 'con' => $mod, 'row_id' => $row_id, 'status' => 1);
        $result = $model->where($map)->count();
        return $result;
    }

    public function getCommentModel()
    {
        return D('Addons://LocalComment/LocalComment');
    }


    //实现的AdminIndex钩子方法
    public function AdminIndex($param)
    {
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
        if ($config['display'])
            $this->display('widget');
    }
}