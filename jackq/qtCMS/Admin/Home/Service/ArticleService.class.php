<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:52
 */

namespace Home\Service;


class ArticleService extends CommonService
{
    //是否一个类型里已经有一个文章
    public function isHavedOnlyOne($cate_id)
    {
        $Article = $this->getM();
        $count = $Article->where("category_id = %d", array($cate_id))->count();
        if (!empty($count) && $count > 0) {
            return true;
        }
        return false;
    }

    //
    public function genCategorySelectOptions($cate_id, $is_edit = false){
        $categoryService = D('Category', 'Service');
        $parents = $categoryService->getCategorysByRelatinModel('Article');
        $pids = array();
        $option = '';
        foreach ($parents as $item) {
            if ($categoryService->existSubCategory($item['id'])) {
                array_push($pids,$item['id']);
                $option .= '<optgroup label="' . $item["name"] . '"></optgroup>__SOPT'.$item['id'].'__';
            }else{
                $option .= $this->genOption($item,$cate_id,$is_edit);
            }
        }
        $opt = array();
        foreach($pids as $pid){
            $subs = $categoryService->getSubCategorys($pid);
            foreach ($subs as $sub) {
                $opt[$pid] .= $this->genOption($sub,$cate_id,$is_edit,true);
            }
        }
        foreach($opt as $key => $value){
            $option=str_replace('__SOPT'.$key.'__',$value,$option);
        }
        return $option;

    }

    private function genOption($category,$cate_id, $is_edit = false,$is_son=false){
        if($is_edit){
            if($category['page_type']==1){
                if ($this->isHavedOnlyOne($category["id"]) && $cate_id != $category['id']) {
                    return;
                }
            }
        }else{
            if($category['page_type']==1){
                if ($this->isHavedOnlyOne($category["id"])) {
                    return;
                }
            }
        }
        $selected = '';
        if ($cate_id == $category["id"]) {
            $selected = 'selected="selected"';
        }
        if($is_son){
            $pre = '&nbsp;&nbsp;|--';
        }
        $option = '<option value="' . $category["id"] . '"  '.$selected.'>'.$pre . $category["name"] . '</option>';
        return $option;
    }


    /**
     * 添加文章
     * @param
     * @return array
     */
    public function add($article)
    {
        $Article = $this->getD();
        $Article->startTrans();
        if (false === ($article = $Article->create($article))) {
            return $this->errorResultReturn($Article->getError());
        }

        $as = $Article->add($article);
        if (false === $as) {
            $Article->rollback();
            return $this->errorResultReturn('系统出错了！');
        }

        $this->saveImage($article['content'], $as);
        $Article->commit();
        return $this->resultReturn(true);
    }

    /**
     * 更新文章
     * @return
     */
    public function update($article)
    {
        $Article = $this->getD();

        if (false === ($article = $Article->create($article))) {
            return $this->errorResultReturn($Article->getError());
        }
        if (false === $Article->save($article)) {
            return $this->errorResultReturn('系统错误！');
        }
        $this->saveImage($article['content'], $article['id'], true);

        return $this->resultReturn(true);
    }

    /**
     * 删除文章
     * @param  int $id 需要删除模型的id
     * @return boolean
     */
    public function delete($id)
    {
        $Article = $this->getD();
        $Article->startTrans();
        // 删除文章
        $delStatus = $Article->delete($id);
        if (false === $delStatus) {
            $Article->rollback();
            return $this->resultReturn(false);
        }
        //删除图片

        $Article->commit();
        return $this->resultReturn(true);
    }

    //
    public function regImages($content)
    {
        $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.bmp|\.bnp]))[\'|\"].*?[\/]?>/i";
        preg_match_all($pattern, $content, $match);
        return $match;
    }

    //判断是否有图片
    public function existImages($content)
    {
        $match = $this->regImages($content);
        // var_dump($match);
        if (empty($match[1]) || sizeof($match[1]) <= 0) {
            return false;
        }
        return true;
    }

    public function saveImage($content, $article_id, $is_update = false)
    {
        $Attachment = M('Attachment');
        if ($is_update) {
            $Attachment->where("attach_type = 1 and article_id =%d ", array($article_id))->delete();
        }
        $match = $this->regImages($content);
        foreach ($match[1] as $img) {
            $data['article_id'] = $article_id;
            $data['attach_type'] = 1;
            $data['url'] = $img;
            $Attachment->add($data);
        }
    }

    private function deleteFile($article_id,$attach_type=1){
        $Attachment = M('Attachment');
        $result = $Attachment->where("attach_type = %d and article_id =%d",array($attach_type,$article_id))->select();
        foreach($result as $attach){

        }
    }

    protected function getModelName()
    {
        return 'Article';
    }
} 