<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 文章的评论接口控制器
 */
class Comment extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
        $this->load->model('comment_model');
        $this->load->model('msg_model');
    }

    /**
     * 添加文章评论
     */
    public function add()
    {
        $article_id = $this->input->post('article_id');
        $comment_content = $this->input->post('comment_content');
        $comment_id = $this->input->post('comment_id');
        $dialog_id = $this->input->post('dialog_id');

        $comment_id = !empty($comment_id) ? $comment_id : null;
        $dialog_id = !empty($dialog_id) ? $dialog_id : null;

        //检查文章是否存在
        $article = $this->article_model->get($article_id);
        if (!is_array($article)) {
            $this->result['error_code'] = -200020;
            return;
        }

        //必须有内容
        if (!$this->simplevalidate->required($comment_content)) {
            $this->result['error_code'] = -200014;
            return;
        }

        //内容长度
        if (!$this->simplevalidate->mix_range($comment_content, 10, 10000)) {
            $this->result['error_code'] = -200015;
            return;
        }

        $comment = array(
            'article_id' => $article_id,
            'comment_content' => $comment_content,
            'user_id' => $this->user['id'],
        );

        //如果收到了comment_id则是回复评论
        if (!empty($comment_id)) {
            //检查被回复评论是否存在
            $reply_comment = $this->comment_model->get($comment_id);
            if (!is_array($reply_comment)) {
                $this->result['error_code'] = -200021;
                return;
            }

            if (!empty($dialog_id)) {
                //根据对话id获取对话人双方用户id，当前评论人用户id在其中则在此对话中增加评论，否则新建对话
                //如果是自己回复自己，双方用户id相同也按正常对话处理，不作特殊处理
                $dialog_userIds = $this->comment_model->get_dialog_userIds($dialog_id);
                //无效对话
                if (!is_array($dialog_userIds)) {
                    $this->result['error_code'] = -200019;
                    return;
                }
                //当前评论人用户id不在对话用户id中，新建对话
                //有一种特殊情况，当用户自己回复自己后，对话id已经生成，但对话人只有1个，这时候别人来回复不需要新生成对话id
                if (!in_array($this->user['id'], $dialog_userIds) && count($dialog_userIds) > 1) {
                    $dialog_id = microtime(true) * 10000;
                    //只有新建对话时才保存被回复的评论id
                    $comment['reply_comment_id'] = $comment_id;
                }
            }
            //如果对话id为空则新建会话
            else {
                $dialog_id = microtime(true) * 10000;
                //只有新建对话时才保存被回复的评论id
                $comment['reply_comment_id'] = $comment_id;
            }
            $comment['dialog_id'] = $dialog_id;
        }

        $comment = $this->comment_model->add($comment);
        if (is_array($comment)) {
            //文章评论量增加1
            $this->article_model->add_comment_counts($article_id);
            $this->result['comment'] = $comment;

            //评论成功后给文章作者消息通知，当然，如果回复自己的文章，就不通知啦
            if ($article['user_id'] != $this->user['id']) {
                $msg_title = '<a href="/u/home/' . $this->user['id'] . '" target="_blank"><i>' . $this->user['nickname'] . '</i></a>回复了您的文章
                              <a target="_blank" href="/q/detail/' . $article['id'] . '"><cite>' . xss_filter($article['article_title']) . '</cite></a>';
                $msg = array(
                    'msg_title' => mb_substr($msg_title, 0, 500, 'UTF-8'),
                    'msg_content' => mb_substr($comment_content, 0, 1000, 'UTF-8'),
                    'receiver_user_id' => $article['user_id'],
                    'sender_user_id' => $this->user['id'],
                );
                $msg = $this->msg_model->add($msg);
            }
            //如果是回复别人的评论，也要通知一下被回复者，当然，同理，回复自己就不通知啦
            if (isset($reply_comment['user_id']) && $reply_comment['user_id'] != $this->user['id']) {
                $msg_title = '<a href="/u/home/' . $this->user['id'] . '" target="_blank"><i>' . $this->user['nickname'] . '</i></a>回复了您的评论
                              <a target="_blank" href="/q/detail/' . $article['id'] . '"><cite>' . xss_filter($article['article_title']) . '</cite></a>';
                $msg = array(
                    'msg_title' => mb_substr($msg_title, 0, 500, 'UTF-8'),
                    'msg_content' => mb_substr($comment_content, 0, 1000, 'UTF-8'),
                    'receiver_user_id' => $reply_comment['user_id'],
                    'sender_user_id' => $this->user['id'],
                );
                $msg = $this->msg_model->add($msg);
            }
        } else {
            $this->result['error_code'] = -200024;
        }
    }

    /**
     * 采纳答案
     */
    public function accept()
    {
        $comment_id = $this->input->post('comment_id');

        $comment = $this->comment_model->get($comment_id);
        $article = $this->article_model->get($comment['article_id']);

        //如果不是文章发布者不能操作采纳答案
        if ($article['user_id'] != $this->user['id']) {
            $this->result['error_code'] = -200217;
            return;
        }

        //将评论状态改为已采纳
        $comment = array(
            'id' => $comment_id,
            'comment_status' => 2,
        );
        $this->comment_model->update($comment);

        //更新提问状态为已采纳
        $article = array(
            'id' => $article['id'],
            'article_status' => 2,
        );
        $this->article_model->update($article);
    }
}
