<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 17:52
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use app\common\model\content\TagsMap;
use app\common\model\content\Tags as TagsModel;
use think\Db;

/**
 * @title 标签管理
 * Class Tags
 * @package app\admin\controller\content
 */
class Tags extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $subsql = Db::table('content_tags_map')
            ->field('tag_id,count(article_id) count')
            ->group('tag_id')
            ->buildSql();

        $db = TagsModel::alias('t')
            ->join([$subsql => 'm'],'m.tag_id = t.id','LEFT')
            ->field('t.*, m.count as article_count');

        return $this->_list($db);
    }

    /**
     * @title 删除操作
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $hasArt = TagsMap::whereIn('tag_id',$ids)->count();
        if ($hasArt){
            $this->error("删除的标签下还有文章，未给予删除");
        }
        $this->_del(new TagsModel(), $ids);
    }
}