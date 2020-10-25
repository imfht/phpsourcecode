<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 冒泡的评论接口控制器
 */
class Maopaocomment extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('maopao_model');
        $this->load->model('maopaocomment_model');
        $this->load->model('msg_model');
    }

    /**
     * 添加冒泡评论
     */
    public function add()
    {
        $maopao_id = $this->input->post('maopao_id');
        $comment_content = $this->input->post('comment_content');
        $comment_id = $this->input->post('comment_id');
        $dialog_id = $this->input->post('dialog_id');

        $comment_id = !empty($comment_id) ? $comment_id : null;
        $dialog_id = !empty($dialog_id) ? $dialog_id : null;

        //检查冒泡是否存在
        $maopao = $this->maopao_model->get($maopao_id);
        if (!is_array($maopao)) {
            $this->result['error_code'] = -200306;
            return;
        }

        //必须有内容
        if (!$this->simplevalidate->required($comment_content)) {
            $this->result['error_code'] = -200304;
            return;
        }

        //内容长度
        if (!$this->simplevalidate->mix_range($comment_content, 10, 10000)) {
            $this->result['error_code'] = -200305;
            return;
        }

        $comment = array(
            'maopao_id' => $maopao_id,
            'comment_content' => $comment_content,
            'user_id' => $this->user['id'],
        );

        //如果收到了comment_id则是回复评论
        if (!empty($comment_id)) {
            //检查被回复评论是否存在
            $reply_comment = $this->maopaocomment_model->get($comment_id);
            if (!is_array($reply_comment)) {
                $this->result['error_code'] = -200307;
                return;
            }

            if (!empty($dialog_id)) {
                //根据对话id获取对话人双方用户id，当前评论人用户id在其中则在此对话中增加评论，否则新建对话
                //如果是自己回复自己，双方用户id相同也按正常对话处理，不作特殊处理
                $dialog_userIds = $this->maopaocomment_model->get_dialog_userIds($dialog_id);
                //无效对话
                if (!is_array($dialog_userIds)) {
                    $this->result['error_code'] = -200308;
                    return;
                }
                //当前评论人用户id不在对话用户id中，新建对话
                if (!in_array($this->user['id'], $dialog_userIds)) {
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

        $comment = $this->maopaocomment_model->add($comment);
        if (is_array($comment)) {
            //冒泡评论量增加1
            $this->maopao_model->add_comment_counts($maopao_id);
            $this->result['comment'] = $comment;
        } else {
            $this->result['error_code'] = -200309;
        }
    }
}
