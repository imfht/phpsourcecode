<?php

/**
 * 网站配置模型
 * Class ConfigModel
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class ConfigModel extends Model
{
    // 数据主表
    public $table = "config";

    // 自动完成
    public $auto = array(
        // 字段名,方法或函数名,方法类型, 处理条件, 处理时机
        array('name', 'strtoupper', 'function', 2, 3),
        // 时间函数自动完成
        array('addtime','time','function',2 ,1),
    );

    //获得配置组
    public function getConfigGroup($isshow = 1)
    {
        return $this->table('config_group')->where(array('isshow'=> $isshow))->order("csort ASC,cid ASC")->all();
    }

    //获得配置组
    public function getConfig($isshow = 1)
    {
        //获得配置组
        $group = $this->table('config_group')->where(array('isshow'=> $isshow))->order("csort ASC,cid ASC")->all();
        foreach ($group as $id => $g)
        {
            $map['cid'] = array('EQ', $g['cid']);
            $config = $this->where($map)->all();
            if ($config)
            {
                foreach ($config as $i => $c)
                {
                    $func = '_' . $c['type'];//text radio select group textarea
                    $config[$i]['_html'] = $this->$func($c);
                }
            }
            $group[$id]['_config'] = $config;
        }
        return $group;
    }


    /**
     * 添加配置
     * @return bool|int
     */
    public function addConfig()
    {
        $name = Q('name', '', 'strtoupper');
        $map['name'] = array('EQ', $name);
        if (M('config')->where($map)->find())
        {
            $this->error = '变量名已经存在';
            return false;
        }
        if (!empty($_POST['info'])) {
            $_POST['info'] = str_replace(' ', '', $_POST['info']);//参数
            $_POST['info'] = String::toSemiangle($_POST['info']);//转拼音
        }
        // 验证变量名
        if (M('config')->find(array('name' => $_POST['name']))) {
            $this->error = '变量名已经存在';
            return false;
        }

        // 数据处理并调用自动完成
        if($this->create())
        {
            if ($this->add())
            {
                return $this->updateCache();
            }
        }
    }

    /**
     * 修改配置
     * @return bool|int
     */
    public function editWebConfig()
    {
        $configData = $_POST['config'];
        if (!is_array($configData))
        {
            $this->error = '数据不能为空';
            return false;
        }
        $db = M('config');
        foreach ($configData as $key => $value)
        {
            $this->where(array('id'=>$key))->save($value);
        }
        return $this->updateCache();
    }



    /**
     * 删除配置
     * @return int
     */
    public function delConfig()
    {
        $id = Q('id', 0, 'intval');
        if ($this->del($id))
        {
            return $this->updateCache();
        }
    }


    /*------------------------属性定义-----------------------------*/
    private function _text($config)
    {
        return "<input type='text' name='config[{$config['id']}][value]' value='{$config['value']}' />";
    }

    private function _radio($config)
    {
        $info = explode(',', $config['info']);
        $html = '';
        foreach ($info as $radio) {
            $data = explode('|', $radio);//[0]值如1  [1]描述如开启
            $checked = $data[0] == $config['value'] ? ' checked="checked" ' : '';
            $html .= "<label><input type='radio' name='config[{$config['id']}][value]' value='{$data[0]}' $checked/> {$data[1]}</label> ";
        }
        return $html;
    }

    private function _textarea($config)
    {
        return "<textarea name='config[{$config['id']}][value]'>{$config['value']}</textarea>";
    }

    //列表选项
    private function _select($config)
    {
        $info = explode(',', $config['info']);
        $html = "<select name='config[{$config['id']}][value]' >";
        foreach ($info as $radio) {
            $data = explode('|', $radio);//[0]值如1  [1]描述如开启
            $selected = $data[0] == $config['value'] ? ' selected="selected" ' : '';
            $html .= "<option value='{$data[0]}' $selected> {$data[1]}</option> ";
        }
        $html .= "</select>";
        return $html;
    }


    //会员组
    private function _group($config)
    {
        $map['admin'] = array('EQ', 0);
        $map['rid'] = array('NEQ', 4);//不是游客
        $memberROle = M('role')->where($map)->all();
        $html = "<select name='config[{$config['id']}][value]' >";
        foreach ($memberROle as $id => $role) {
            $selected = $role['rname'] == $config['value'] ? ' selected="" ' : '';
            $html .= "<option value='{$role['rid']}' $selected>{$role['rname']}</option>";
        }
        $html .= "</select>";
        return $html;
    }



    /**
     * 更新配置文件
     * @return int
     */
    public function updateCache()
    {
        $configData = $this->order('sort ASC,id ASC')->all();
        $data = array();
        foreach ($configData as $c) {
            $name = strtoupper($c['name']);
            $data[$name] = $c['value'];
        }
        //写入配置文件
        $content = "<?php if (!defined('HDPHP_PATH')) exit; \nreturn " . var_export($data, true) . ";\n?>";
        return file_put_contents("Data/Config/config.inc.php", $content);
    }
}