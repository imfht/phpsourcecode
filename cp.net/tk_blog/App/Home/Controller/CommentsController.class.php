<?php
/**
 * 评论处理
 */
namespace Home\Controller;
use Common\Controller\HomebaseController;
class CommentsController extends  HomebaseController {
    /**
     * 用户评论信息 列表
     */
    public function index(){
        $uid = $_SESSION['user']['uid'];
        //清空消息推送数据
        set_msg ($uid,1,true);
        $result = pageShow($this->getComments($uid),3);
        $this->assign('CommentData',$result['data']);
        $this->assign('total',$result['total']);
        $this->assign('page',$result['page']);
        $this->assign('pg_limit',$result['limit']);
        $this->assign('pg_total',$result['total']);
        $this->assign('cocolait','Cocolait博客-评论消息');
        $this->display();
    }

    /**
     * 获取被评论的 消息
     * @param $uid
     */
    protected function getComments($uid){
        //获取已审核的评论数据
        $data = M('Comments')->where(array('to_uid'=>$uid,'status'=>1))->order('createtime DESC')->select();
        foreach($data as $k=>$v){
            //获取回复者 用户昵称和图片
            $oldData = M('Users')->where(array('uid'=>$v['uid']))->field('nickname,face')->find();
            $data[$k]['nickname'] = $oldData['nickname'];
            $data[$k]['img'] = $oldData['face'];

            //获取被回复者 用户昵称和图片（自己）
            $toData = M('Users')->where(array('uid'=>$uid))->field('nickname,face')->find();
            $data[$k]['toNickname'] = $toData['nickname'];
            $data[$k]['toImg'] = $toData['face'];

            //获取上一次的记录
            $nextData= M('Comments')->where(array('id'=>$v['post_id']))->find();
            $title = M('Article')->where(array('aid'=>$nextData['aid']))->getField('title');
            if ($nextData) {
                $nData = M('Users')->where(array('uid'=>$nextData['uid']))->field('nickname,face')->find();
                $nextData['nickname'] = $nData['nickname'];
                $nextData['img'] = $nData['face'];
                $nextData['title'] = $title;
                $data[$k]['child'][] = $nextData;
            }
        }
        return $data;
    }

    /**
     * ajax 处理多层评论
     */
    public function send_comment(){
        if (IS_AJAX) {
            //评论者ID
            $uid = I('post.uid',0,'intval');
            //文章ID
            $aid = I('post.aid',0,'intval');
            //被评论的用户id
            $toUid = I('post.toUid',0,'intval');
            //用于前端js  页面标点内容插入
            $oldMid = I('post.mid',0,'intval');
            //评论层级
            $level = I('post.level',0,'intval');
            //用于判断多层回复  是否出现“回复”
            $diff = I('post.diff',0,'intval');
            //上一次评论的id
            $post_id = I('post.post_id',0,'intval');
            //查询被评论的昵称
            if ($toUid) {
                $toNickname = M('Users')->where(array('uid'=>$toUid))->getField('nickname');
                if ($toUid == $uid) {
                    $toNickname = '我';
                }
            }

            //父类ID
            $parentId = I('post.parentId',0,'intval');
            //评论内容
            $content = I('post.comment','');
            $commentModel = M('comments');
            if (!$uid || !$content || !$aid) {
                exit(json_encode(array('status'=>0,'msg'=>'操作失败.ㄒoㄒ~','level'=>'')));
            }
            //写入数据
            $postData = array(
                'url' => I('post.url',''),
                'uid' => $uid,
                'full_name' => $_SESSION['user']['nickname'],
                'email' => $_SESSION['user']['u_email'],
                'createtime' => date('Y-m-d H:i:s',time()),
                'content' => replace_phiz($content),
                'aid' => $aid,
                'post_id' => $post_id,
            );
            $postData['to_uid'] = $toUid;//被评论的用户ID
            $postData['parentid'] = $parentId;//父类ID
            $time = date('Y/m/d H:i',time());
            $img = $_SESSION['user']['face'];//头像
            $nickname = $postData['full_name'];//昵称
            $newContent = $postData['content'];//内容
            if (!$level) {
                //一层评论
                if ($mid = $commentModel->add($postData)) {
                    $oldMid = empty($oldMid) ? $mid : $oldMid;
                    //组合字符串返回
                    $str=<<<str
                    <li id="comment-{$mid}" class="comment">
							<article id="div-comment-{$mid}" class="comment-body">
								<footer class="comment-meta">
									<div class="comment-author">
										<img alt="{$nickname}" src="{$img}" class="avatar avatar-70 photo" style="width:70px;height:70px;">
										<b class="fn"><a href="" rel="external nofollow" class="url">{$nickname}</a></b>
										<span class="says">说道：</span>
									</div>

									<div class="comment-metadata">
										<a href="javascript:;">
											<time datetime="{$time}">{$time}</time>
										</a>
									</div>

								</footer>

								<div class="comment-content">
									<p>{$newContent}</p>
								</div>

								<div class="reply">
									<a class="comment-reply-link" href="javascript:;" toUid={$uid} parentId="{$mid}" comid="{$oldMid}" level="1" postID="{$mid}">回复</a>
								</div>
							</article>
							<ol class="children">
								<span id="show-comment-{$mid}"></span>
							</ol>
                    </li>
str;
                    //往内存 推送消息 给用户
                    set_msg($toUid,1);
                    //给作者发送邮件
                    cp_sendEmaill_to_User($aid,$nickname,replace_phiz($content,true),$mid);
                    exit(json_encode(array('status'=>1,'msg'=>'评论成功.^_^','level'=>$level,'data'=>$str)));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'操作失败.ㄒoㄒ~','level'=>'0')));
                }


            } else {

                if ($mid = $commentModel->add($postData)) {
                    //多层评论
                    $str = <<<str
                    <li id="comment-{$mid}" class="comment">
                        <article id="div-comment-{$mid}" class="comment-body">
                            <footer class="comment-meta">
                                <div class="comment-author">
                                    <img alt="{$nickname}" src="{$img}" class="avatar avatar-70 photo" style="width:70px;height:70px;">
                                    <b class="fn"><a href="" rel="external nofollow" class="url">{$nickname}</a></b>
                                    <span class="says">回复<span style="margin-left: 7px;">{$toNickname}</span>：</span>
                                </div>
                                <div class="comment-metadata">
                                    <a href="javascript:;">
                                        <time datetime="{$time}">{$time}</time>
                                    </a>
                                </div>
                            </footer>

                            <div class="comment-content">
                                <p>{$newContent}</p>
                            </div>

                            <div class="reply">
                                <a class="comment-reply-link" href="javascript:;" toUid={$uid} parentId="{$parentId}" comid="{$oldMid}" level="1" postID="{$mid}">回复</a>
                            </div>
                        </article>

                    </li>

str;
                    if ($diff) {
                        //去掉"回复"标签
                        //多层评论
                        /*$str = <<<str
                        <li id="comment-{$mid}" class="comment">
                            <article id="div-comment-{$mid}" class="comment-body">
                                <footer class="comment-meta">
                                    <div class="comment-author">
                                        <img alt="{$nickname}" src="{$img}" class="avatar avatar-70 photo" style="width:70px;height:70px;">
                                        <b class="fn"><a href="" rel="external nofollow" class="url">{$nickname}</a></b>
                                        <span class="says">回复<span style="margin-left: 7px;">{$newNickname}</span>：</span>
                                    </div>
                                    <div class="comment-metadata">
                                        <a href="javascript:;">
                                            <time datetime="{$time}">{$time}</time>
                                        </a>
                                    </div>
                                </footer>
                                <div class="comment-content">
                                    <p>{$newContent}</p>
                                </div>
                            </article>
                        </li>

str;*/
                        //往内存 推送消息 给用户
                        set_msg($toUid,1);
                        //给作者发送邮件
                        cp_sendEmaill_to_User($aid,$nickname,replace_phiz($content,true),$mid);
                        exit(json_encode(array('status'=>1,'msg'=>'回复成功.^_^','level'=>$level,'data'=>'','tag'=>"#show-comment-{$oldMid}")));
                    }
                    //往内存 推送消息 给用户
                    set_msg($toUid,1);
                    //给作者发送邮件
                    cp_sendEmaill_to_User($aid,$nickname,replace_phiz($content,true),$mid);
                    exit(json_encode(array('status'=>1,'msg'=>'评论成功.^_^','level'=>$level,'data'=>$str,'tag'=>"#show-comment-{$oldMid}")));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'操作失败.ㄒoㄒ~','level'=>'1')));
                }



            }
        }
    }





    /**
     * 异步轮询推送消息
     */
    public function getMsg () {
        if (IS_AJAX) {
            //读取内存缓存数据
            $uid = $_SESSION['user']['uid'];
            $msg = S('usermsg' . $uid);

            if ($msg) {
                //评论 推送数据
                if ($msg['comment']['status']) {
                    $msg['comment']['status'] = 0;
                    S('usermsg' . $uid, $msg, 0);
                    echo json_encode(array(
                        'status' => 1,
                        'total' => $msg['comment']['total'],
                        'type' => 1
                    ));
                    exit();
                }
            }
            echo json_encode(array('status' => 0));
        }
    }

}