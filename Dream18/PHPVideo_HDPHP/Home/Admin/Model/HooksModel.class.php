<?php

/**
 * 钓子处理模型
 * Class HookModel
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class HooksModel extends Model
{
    public $table = 'hooks'; //表名
    private $id; //钓子ID

    public function __init()
    {
        $this->id = Q('id', 0, 'intval');
    }

    //更新钓子
    public function updateHook()
    {
        //验证钓子是否存在
        $where = "id<>" . $_POST['id'] . " AND name='{$_POST['name']}'";
        if ($this->where($where)->find()) {
            $this->error = '钓子已经存在';
            return;
        }
        return $this->save();
    }

    //添加钓子
    public function addHook()
    {
        //验证钓子是否存在
        if ($this->where(array('name' => Q('name')))->find()) {
            $this->error = '钓子已经存在';
            return;
        }
        //验证规则
        $this->validate = array(
            array('name', 'nonull', '钩子名子不能为空', 2, 3),
            array('description', 'nonull', '钩子名子不能为空', 2, 3)
        );
        if ($this->create()) {
            if ($this->add()) {
                return true;
            } else {
                $this->error = '添加失败';
            }
        }
    }

    //删除钓子
    public function delHook()
    {
        if($this->del($this->id)){
            return true;
        }else{
            $this->error='删除失败';
        }
    }

    /**
     * 去除所有钩子中对应的插件数据
     * @param $addon_name
     * @return mixed
     */
    public function removeHooks($addon_name)
    {
        $addonObj = $this->getAddonObj($addon_name); //插件对象
        $method = get_class_methods($addonObj); //钩子方法
        $hooks = M('hooks')->getField('name', true); //所有钩子
        $common = array_intersect($hooks, $method);
        if ($common) {
            foreach ($common as $hook) {
                $status = $this->removeAddon($hook, array($addon_name));
                if (!$status) return false;
            }
        }
        return true;
    }


    /**
     * 更新钓子插件
     * @param $addon_name
     * @return mixed
     */
    public function updateHooks($addon_name)
    {
        //插件对象
        $addonObj = $this->getAddonObj($addon_name);
        if (!$addonObj) return false;
        //获得插件钓子方法
        $method = get_class_methods($addonObj);
        //钓子名
        $hooks = $this->getField('name', true);
        $common = array_intersect($hooks, $method);
        if (!empty($common)) {
            foreach ($common as $hook) {
                $status = $this->updateAddons($hook, array($addon_name));
                if (!$status) {
                    $this->removeHooks($addon_name);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 更新单一插件
     * @param $hook 钩子名
     * @param $addon_name 插件名
     */
    public function updateAddons($hook, $addon_name)
    {
        $addons = $this->where(array('name' => $hook))->getField('addons');
        if ($addons){
            $addons = explode(',', $addons);
        }
        if ($addons) {
            $addons = array_merge($addons, $addon_name);
            $addons = array_unique($addons);
        } else {
            $addons = $addon_name;
        }
        $status = M('hooks')->where(array('name' => $hook))->save(array('addons' => implode(',', $addons)));
        return $status;
    }
    /**
     * 移除一个钩子中的插件
     * @param $hook 钩子名
     * @param $addon_name 插件名
     * @return mixed
     */
    public function removeAddon($hook, $addon_name)
    {
        $addons = M('hooks')->where(array('name' => $hook))->getField('addons', true);
        if ($addons) {
            $addons = array_diff($addon_name, $addons);
        } else {
            return true;
        }
        return M('hooks')->where(array('name' => $hook))->save(array('addons' => implode(',', $addons)));
    }
    /**
     * 获得插件对象
     * @param $addon 插件名
     * @return mixed;
     */
    public function getAddonObj($addon)
    {
        $classFile = APP_ADDON_PATH . $addon . '/' . $addon . 'Addon.class.php';
        if (!is_file($classFile)) return false;
        require_cache($classFile);
        $class = $addon . 'Addon'; //类名
        if (!class_exists($class)) {
            return false;
        }
        return new $class;
    }
}