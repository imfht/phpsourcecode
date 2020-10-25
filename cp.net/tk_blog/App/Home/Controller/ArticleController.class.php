<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
class ArticleController extends  HomebaseController {
    public function index(){
        $aid = I('get.aid',0,'intval');
        //检测恶意输入
        if (!checkId('aid',$aid)) {
            //直接跳至首页
            redirect(__ROOT__.'/');
            exit();
        }
        $articleModel = new \Common\Model\ArticleModel();
        $articleModel->where(array('aid'=>$aid))->setInc('hits');
        $this->assign('data',$articleModel->getFindData($aid));
        $result = pageShow($this->_getCommentData($aid),5);
        $this->assign('CommentData',$result['data']);
        $this->assign('page',$result['page']);
        $this->assign('pg_limit',$result['limit']);
        $this->assign('pg_total',$result['total']);
        $this->assign('Counts',M('Comments')->where(array('aid'=>$aid))->count());
        $this->assign('cocolait','Cocolait博客-' . $this->data['title']);
        $this->display();
    }

    /**
     * 获取所有的评论数据
     */
    protected function _getCommentData($aid){
        //获取已审核的评论数据
        $data = M('Comments')->where(array('aid'=>$aid,'status'=>1))->order('createtime ASC')->select();
        foreach($data as $k=>$v){
            $oldData = M('Users')->where(array('uid'=>$v['uid']))->field('nickname,face')->find();
            $data[$k]['nickname'] = $oldData['nickname'];
            $data[$k]['img'] = $oldData['face'];
            if ($v['to_uid']) {
                if ($v['to_uid'] == $_SESSION['user']['uid']) {
                    $data[$k]['toNickname'] = '我';
                } else {
                    $data[$k]['toNickname'] = M('Users')->where(array('uid'=>$v['to_uid']))->getField('nickname');
                }
            } else {
                $data[$k]['toNickname'] = '';
            }
        }
        return node_merge_comment($data);
    }

    /**
     * 文章点赞
     */
    public function praise(){
        if (IS_AJAX) {
            $aid = I('post.aid',0,'intval');
            if (!$aid) exit(json_encode(array('status'=>0,'msg'=>'非法请求')));
            $uid = I('post.uid',0,'intval');
            $model = M('Praise');
            $now = time() + 3600;//保留一小时 本站用户
            $where = array('uid'=>$uid,'aid'=>$aid,'type'=>1);
            $data = array(
                'user_ip' => get_client_ip(0,true),
                'add_time'=> time(),
                'type' => 1,
                'uid' => $uid,
                'aid' => $aid,
                'valid_time' => $now,
                'nums' => 1,
            );
            if ($uid) {
                //本站用户点赞
                $oldData = $model->where($where)->find();
                if (!$oldData) {
                    //未点赞
                    //组合数据 执行添加操作
                    if ($model->add($data)) {
                        //修改文章点赞数
                        M('Article')->where(array('aid'=>$aid))->setInc('praise');
                        exit(json_encode(array('status'=>1,'msg'=>'点赞成功.^_^')));
                    } else {
                        exit(json_encode(array('status'=>0,'msg'=>'点赞失败,请重试ㄒoㄒ~')));
                    }
                } else {
                    //已点赞
                    //判断时间是否可点赞
                    if ($oldData['valid_time'] < $now) {
                        exit(json_encode(array('status'=>0,'msg'=>'您已经点过赞了,一小时后再来吧.^_^')));
                    } else {
                        //执行修改操作
                        $data['nums'] = $oldData['nums'] + 1;
                        if($model->where($where)->save($data)){
                            //修改文章点赞数
                            M('Article')->where(array('aid'=>$aid))->setInc('praise');
                            exit(json_encode(array('status'=>1,'msg'=>'点赞成功.^_^')));
                        } else {
                            exit(json_encode(array('status'=>0,'msg'=>'点赞失败,请重试ㄒoㄒ~')));
                        }
                    }
                }

            } else {
                //游客
                $now = time() + (3600 * 24);//保留一天
                $data['uid'] = 0;
                $data['type'] = 0;
                $data['valid_time'] = $now;
                $where2 = array('aid'=>$aid,'user_ip'=>$data['user_ip'],'type'=>0);
                $oldData = $model->where($where2)->find();
                if ($oldData) {
                    //已点赞
                    //判断时间是否可点赞
                    if ($oldData['valid_time'] < $now) {
                        exit(json_encode(array('status'=>0,'msg'=>'您已经点过赞了,一天后再来吧.^_^')));
                    } else {
                        //执行修改操作
                        $data['nums'] = $oldData['nums'] + 1;
                        if($model->where($where2)->save($data)){
                            //修改文章点赞数
                            M('Article')->where(array('aid'=>$aid))->setInc('praise');
                            exit(json_encode(array('status'=>1,'msg'=>'点赞成功.^_^')));
                        } else {
                            exit(json_encode(array('status'=>0,'msg'=>'点赞失败,请重试ㄒoㄒ~')));
                        }
                    }
                } else {
                    //未点赞
                    //组合数据 执行添加操作
                    if ($model->add($data)) {
                        //修改文章点赞数
                        M('Article')->where(array('aid'=>$aid))->setInc('praise');
                        exit(json_encode(array('status'=>1,'msg'=>'点赞成功.^_^')));
                    } else {
                        exit(json_encode(array('status'=>0,'msg'=>'点赞失败,请重试ㄒoㄒ~')));
                    }
                }
            }
        }
    }
}