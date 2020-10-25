<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 17:39
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use app\common\model\content\Link as LinkModel;

/**
 * @title 友情链接
 * Class Link
 * @package app\admin\controller\content
 */
class Link extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index(){
        return $this->_list(new LinkModel());
    }

    /**
     * @title 添加
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new LinkModel(), 'form');
    }

    /**
     * @title 编辑操作
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new LinkModel(), 'form');
    }

    protected function _form_before($data)
    {
        if ($this->request->isPost()){
            if (strpos($data['href'],'http') !== 0){
                $this->error("请以http://或https://开头");
            }
        }
    }

    /**
     * @title 删除操作
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $this->_del(new LinkModel(), $ids);
    }

    /**
     * @title 禁用/启用
     */
    public function change()
    {
        $id = $this->request->post('id');
        $state = $this->request->post('state');
        $this->_change(new LinkModel(), $id, ['state' => $state]);
    }
}