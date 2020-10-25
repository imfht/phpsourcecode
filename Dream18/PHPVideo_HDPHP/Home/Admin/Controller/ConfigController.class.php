<?php
/**
 * 配置项管理控制器
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class ConfigController extends AuthController
{
	/**
     * 数据对象
     * [$db description]
     * @var [type]
     */
	private $db;

	/**
     * 构造函数
     * [__init description]
     * @return [type] [description]
     */
	public function __init()
	{
		parent::__init();
		$this->db = K('Config');
	}


    /**
     * 添加配置项
     */
    public function add()
    {
        if (IS_POST)
        {
            if ($this->db->addConfig())
            {
                $this->success('添加成功!', 'index');
            }
            $this->error($this->db->error);
        }
        else
        {
            $configGroup = $this->db->getConfigGroup();
            $this->assign("configGroup", $configGroup);
            $this->display();
        }
    }



    /**
     * 修改网站配置(基本配置）
     * @return [type] [description]
     */
    public function webConfig()
    {
        if (IS_POST)
        {
            if ($this->db->editWebConfig())
            {
                $this->success("修改成功");
            }
            $this->error($this->db->error);
        }
        else
        {
            //分配配置组
            $data = $this->db->getConfig();
            $this->assign('data', $data);
            $this->display();
        }
    }



    /**
     * 更新缓存
     * @return [type] [description]
     */
    public function updateCache()
    {
        if ($this->db->updateCache())
        {
            $this->success('缓存更新成功！');
        }
        $this->error($this->db->error);
    }

    /**
     * 删除配置
     * @return [type] [description]
     */
    public function del()
    {
        if ($this->db->delConfig())
        {
            $this->success('操作成功');
        }
        $this->error($this->db->error);
    }
}