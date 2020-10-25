<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\BaseModel;

/**
 * 文章-模型
 * @author 牧羊人
 * @since 2020/7/11
 * Class Article
 * @package app\admin\model
 */
class Article extends BaseModel
{
    // 设置数据表名
    protected $name = 'article';

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id);
        if ($info) {
            // 文章封面
            if ($info['cover']) {
                $info['cover_url'] = get_image_url($info['cover']);
            }

            // 获取栏目
            if ($info['cate_id']) {
                $itemCateMod = new ItemCate();
                $itemCateInfo = $itemCateMod->getInfo($info['cate_id']);
                $info['cate_name'] = $itemCateInfo['item_name'] . ">>" . $itemCateInfo['name'];
            }

            // 获取分表
            $table = $this->getArticleTable($id, false);
            $articleMod = db($table);
            $articleInfo = $articleMod->find($id);
            if ($articleInfo['content']) {
                while (strstr($articleInfo['content'], "[IMG_URL]")) {
                    $articleInfo['content'] = str_replace("[IMG_URL]", IMG_URL, $articleInfo['content']);
                }
            }
            $info = array_merge($info, $articleInfo);

            // 文章图集
            if ($info['imgs']) {
                $imgsList = unserialize($info['imgs']);
                foreach ($imgsList as &$val) {
                    $val = get_image_url($val);
                }
                $info['imgsList'] = $imgsList;
            }

        }
        return $info;
    }

    /**
     * 添加或编辑
     * @param array $data
     * @param string $error
     * @param bool $isSql
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @since 2020/7/11
     */
    public function edit($data = [], &$error = '', $isSql = false)
    {
        // 获取数据表字段
        $column = $this->getTableFields();

        $item = [];
        foreach ($data as $key => $val) {
            if (!in_array($key, $column)) {
                $item[$key] = $val;
                unset($data[$key]);
            }
        }

        //开启事务
//        $this->startTrans();
        $rowId = parent::edit($data, $error, $isSql);
        if (!$rowId) {
            //事务回滚
//            $this->rollback();
            return false;
        }
        $result = $this->saveArticleInfo($rowId, $item);
        if (!$result) {
            //事务回滚
//            $this->rollback();
            return false;
        }
        //提交事务
//        $this->commit();
        return $rowId;
    }

    /**
     * 保存附表信息
     * @param $id
     * @param $item
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @since 2020/7/11
     */
    public function saveArticleInfo($id, $item)
    {
        $table = $this->getArticleTable($id);
        $info = $this->where(['id' => $id])->table($table)->find();

        $data = [];
        if (!$info) {
            $data['id'] = $id;
        }
        $data['content'] = $item['content'];
        if ($item['guide']) {
            $data['guide'] = $item['guide'];
        }
        if ($item['imgs']) {
            $data['imgs'] = $item['imgs'];
        }

        //获取分表模型
        $table = $this->getArticleTable($id, false);
        $articleMod = db($table);
        if ($info['id']) {
            $result = $articleMod->where('id', $id)->update($data);
        } else {
            $result = $articleMod->insert($data);
        }
        if ($result !== false) {
            return true;
        }
        return false;
    }

    /**
     * 获取附表名称
     * @param $id
     * @param bool $isPrefix
     * @return string
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function getArticleTable($id, $isPrefix = true)
    {
        $table = substr($id, -1, 1);
        $table = "article_{$table}";
        if ($isPrefix) {
            $table = DB_PREFIX . $table;
        }
        return $table;
    }
}
