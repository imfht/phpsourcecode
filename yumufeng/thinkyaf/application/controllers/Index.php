<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

class IndexController extends Rest
{

    /**
     * GET /Index/index?data=''
     * GET请求测试
     *
     * success() 和 fail() 快速返回示例
     */
    public function GET_indexAction()
    {
        return $this->success('index/index');
    }

    /**
     * POST /Index/addUser
     * POST请求测试
     *
     * response()函数自定义状态
     */
    public function POST_addUserAction()
    {
        $where['username'] = input('username');
        $where['password'] = input('password');
        //数据验证
        $video_validate = new \validate\User();
        //采用场景验证
        if (!$video_validate->scene('add')->check($where)) {
            $this->error($video_validate->getError());
        }
        //简单的模型调用
        $user_model = new UserModel();
        if ($user_model->add($where)) {
            $this->success();
        }
        $this->error();

    }
}