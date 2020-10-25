<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;

/**
 * 文章逻辑
 */
class Article extends LogicBase
{

    // 文章模型
    public static $articleModel = null;

    /**
     * 构造方法
     */
    public function __construct()
    {

        parent::__construct();

        self::$articleModel = model($this->name);
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];
        if (!empty($data['tid'])) {

            $where['tid'] = $data['tid'];
        } else {

        }
        !empty($data['search_data']) && $where['name'] = ['like', '%' . $data['search_data'] . '%'];

        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 获取文章列表
     */
    public function getArticleList($where = [], $field = true, $order = '', $paginate = 0)
    {
        return self::$articleModel->getList($where, 'm.*,user.username,articlecate.name as tidname', $order, 0, [['user', 'm.uid=user.id', 'LEFT'], ['articlecate', 'm.tid=articlecate.id', 'LEFT']]);
        // return self::$articleModel->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取文章信息
     */
    public function getArticleInfo($where = [], $field = true)
    {

        return self::$articleModel->getInfo($where, $field);
    }


    /**
     * 文章添加
     */
    public function articleAdd($data = [])
    {

        $validate = validate($this->name);

        $validate_result = $validate->scene('add')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;

        $url = url('configList');

        $data['content'] = htmlspecialchars_decode($data['content']);

        return self::$articleModel->setInfo($data) ? [RESULT_SUCCESS, '文章添加成功', $url] : [RESULT_ERROR, self::$articleModel->getError()];
    }

    /**
     * 文章编辑
     */
    public function articleEdit($data = [])
    {

        $validate = validate($this->name);

        $validate_result = $validate->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;

        $url             = url('configList');
        $data['content'] = htmlspecialchars_decode($data['content']);
        return self::$articleModel->setInfo($data) ? [RESULT_SUCCESS, '文章编辑成功', $url] : [RESULT_ERROR, self::$articleModel->getError()];
    }

    /**
     * 设置文章信息
     */
    public function setArticleValue($where = [], $field = '', $value = '')
    {

        return self::$articleModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$articleModel->getError()];
    }

    /**
     * 文章删除
     */
    public function articleDel($where = [])
    {

        return self::$articleModel->deleteInfo($where) ? [RESULT_SUCCESS, '文章删除成功'] : [RESULT_ERROR, self::$articleModel->getError()];
    }

    /**
     * 批量删除
     */
    public function articleAlldel($ids)
    {


        return self::$articleModel->deleteAllInfo(['id' => ['in', $ids]]) ? [RESULT_SUCCESS, '文章删除成功'] : [RESULT_ERROR, self::$articleModel->getError()];
    }


}
