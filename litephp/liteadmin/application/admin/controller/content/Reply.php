<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/11
 * Time: 15:28
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use app\common\model\content\ArticleReply;

/**
 * @title 文章评论
 * Class Reply
 * @package app\admin\controller\content
 */
class Reply extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        return $this->_list(new ArticleReply());
    }

    /**
     * @title 禁用/启用
     */
    public function change()
    {
        $id = $this->request->post('id');
        $state = $this->request->post('state');
        $this->_change(new ArticleReply(), $id, ['state' => $state]);
    }

    /**
     * @title 删除操作
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $this->_del(new ArticleReply(), $ids);
    }
}