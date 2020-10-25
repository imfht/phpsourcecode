<?php
/**
 * 更新缓存控制器
 * @author 楚羽幽 <Name_Cyu@Foxmai.com>
 */
class CacheController extends AuthController
{
    /**
     * [index 缓存视图]
     * @return [type] [description]
     */
    public function index()
    {
        if (IS_POST)
        {
            is_file(TEMP_PATH . '~Boot.php') && unlink(TEMP_PATH . '~Boot.php');
            Dir::del('Temp/Compile');
            Dir::del('Temp/Content');
            Dir::del('Temp/Table');
            // 缓存更新动作
            S('updateCacheAction', $_POST['Action']);
            $this->success('正在准备更新全站缓存...', U('updateCache', array('action' => 'Config')), 1);
        } 
        $this->display();
    }

    /**
     * [updateCache 更新缓存]
     * @return [type] [description]
     */
    public function updateCache()
    {
        $actionCache = S('updateCacheAction');
        if ($actionCache)
        {
            while ($action = array_shift($actionCache))
            {
                switch ($action)
                {
                    case "Config" :
                        $Model = K("Config");
                        $Model->updateCache();
                        S('updateCacheAction',$actionCache);
                        $this->success('网站配置更新完毕...', U('updateCache'), 1);
                    break;
                    case "Cate" :
                        $Model = K('Cate');
                        $Model->updateCache();
                        S('updateCacheAction',$actionCache);
                        $this->success('频道栏目更新完毕...', U('updateCache'), 1);
                    break;
                    case "Addons" :
                        $Model = K('Addons');
                        $Model->updateAddonCache();
                        S('updateCacheAction',$actionCache);
                        $this->success('插件缓存更新完毕...', U('updateCache'), 1);
                    break;
                }
            }
            go('updateCache');
        }
        else
        {
            S('updateCacheAction', null);
            $this->success('全站缓存更新成功...', U('index'));
        }
    }
}
