<?php

namespace app\admin\logic;

use think\Model;

/**
 * Description of DocumentArticle
 * 文章详情
 * @author static7
 */
class DocumentArticle extends Model {

    /**
     * 获取模型详细信息
     * @param  integer $id 文档ID
     * @return array       当前模型详细信息
     * @author staitc7 <static7@qq.com>
     */
    public function detail(int $id = 0) {
        $data = $this::get(function ($q)use($id) {
                    $q->where('id', $id);
                });
        if (!$data) {
            return $this->error = '获取详细信息出错！';
        }
        return $data;
    }

    /**
     * 添加或者更新文章
     * @param array $article 数组
     * @author staitc7 <static7@qq.com>
     * @return bool
     */
    public function renew(array $article = []) {
        $exist= $this::where('id',(int) $article['id'])->value('id');
        $object = $exist ? $this::update($article) : $this::create($article);
        unset($exist);
        return $object ? true : false;
    }

    /* ===================自动完成====================== */
}
