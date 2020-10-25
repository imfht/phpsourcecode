<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 文章管理接口控制器
 */
class Article extends AdminAPI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
        $this->load->model('articleappend_model');
        $this->load->model('articletopic_model');
        $this->load->model('comment_model');
        $this->load->model('articlevote_model');
        $this->load->model('commentvote_model');
    }

    /**
     * 置顶/取消置顶
     */
    public function set_top()
    {
        $article_id = $this->input->post('article_id');
        $is_top = $this->input->post('is_top');

        $is_top = $is_top == 1 ? 2 : 1;

        $article = array(
            'id' => $article_id,
            'is_top' => $is_top,
        );
        $this->article_model->update($article);
    }

    /**
     * 加精/取消加精
     */
    public function set_fine()
    {
        $article_id = $this->input->post('article_id');
        $is_fine = $this->input->post('is_fine');

        $is_fine = $is_fine == 1 ? 2 : 1;

        $article = array(
            'id' => $article_id,
            'is_fine' => $is_fine,
        );
        $this->article_model->update($article);
    }

    /**
     * 删除
     */
    public function del()
    {
        $article_id = $this->input->post('article_id');

        //删除文章
        $this->article_model->del($article_id);
        //删除文章追加内容
        $this->articleappend_model->del_by_articleId($article_id);
        //删除文章话题
        $this->articletopic_model->del_by_articleId($article_id);
        //删除评论
        $this->comment_model->del_by_articleId($article_id);
        //删除文章投票信息
        $this->articlevote_model->del_by_articleId($article_id);
        //删除评论投票信息
        $this->commentvote_model->del_by_articleId($article_id);
    }
}
