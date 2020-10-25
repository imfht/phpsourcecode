<?php

namespace Addons\Favorites;
use Common\Controller\Addon;

/**
 * 收藏插件
 * @author 大蒙
 */

    class FavoritesAddon extends Addon{

        public $info = array(
            'name'=>'Favorites',
            'title'=>'收藏',
            'description'=>'发布内容的收藏',
            'status'=>1,
            'author'=>'大蒙',
            'version'=>'0.1'
        );

public function install()
    {
        $db_prefix = C('DB_PREFIX');
        $sql = "
CREATE TABLE IF NOT EXISTS `{$db_prefix}favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appname` varchar(20) NOT NULL COMMENT '应用名',
  `row` int(11) NOT NULL COMMENT '应用标识',
  `uid` int(11) NOT NULL COMMENT '用户',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `table` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='支持的表'  ;
        ";
        $rs = D('')->execute($sql);
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的checkin钩子方法
    public function favorites($param)
    {

       $param['jump']=isset($param['jump'])?$param['jump']:'';
        $this->assign($param);

        $map_favorites['appname'] = $param['app'];
        $map_favorites['table'] = $param['table'];
        $map_favorites['row'] = $param['row'];

        $count = $this->getFavoritesCountCache($map_favorites);

        $map_favoritesed = array_merge($map_favorites, array('uid' => is_login()));
        $favoritesed = D('Favorites')->where($map_favoritesed)->count();


        $this->assign('count', $count);
        $this->assign('favoritesed', $favoritesed);
        $this->display('favorites');

    }

    /**
     * @param $map_favorites
     * @return mixed
     * @auth dameng
     */
    private function getFavoritesCountCache($map_favorites)
    {
        $cache_key = "favorites_count_" . implode('_', $map_favorites);
        $count = S($cache_key);
        if (empty($count)) {
            $count = D('Favorites')->where($map_favorites)->count();
            S($cache_key, $count);
            return $count;
        }
        return $count;
    }



    }