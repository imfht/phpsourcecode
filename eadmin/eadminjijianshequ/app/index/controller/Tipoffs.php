<?php

namespace app\index\controller;

use app\common\controller\HomeBase;
use esclass\database;

class Tipoffs extends HomeBase
{
    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 新增举报，每次内容每个人限举报一次
     *
     */
    public function sendInfo()
    {
        if (IS_POST) {
            $uid = is_login();//获取登录人Id
            if (empty($uid)) {
                $this->jump(RESULT_ERROR, '举报失败,您尚未登录',es_url('User/login'));
            }

            $pid = $this->param['userid'];//被举报人Id
            if (empty($pid)) {
                $this->jump(RESULT_ERROR, '举报失败,被举报人不存在');
            }

            if ($pid == $uid) {
                $this->jump(RESULT_ERROR, '举报失败,不能举报自己');
            }

            $userinfo    = db('user')->where(['id' => $pid])->getRow();
            $contentId   = $this->param['contentId'];//被举报人Id
            $contentType = $this->param['contentType'];
            $tipoffs     = db('tipoffs')->where(['prosecutorId' => $uid,
                                                 'defendantId'  => $pid,
                                                 'contentId'    => $contentId,
                                                 'contentType'  => $this->param['type']])
                ->getRow();
            if (!empty($tipoffs) && !empty($tipoffs['id'])) {
                $this->jump(RESULT_ERROR, '举报失败,您对当前信息已提交过举报,请静待处理！再次感谢您对网络净化的支持。', '', ['data' => $tipoffs]);
            }
            $type = $this->param['type'];
            switch ($type) {
                case 0:
                    $content = db('comment')->where(['id' => $contentId])->getRow();
                    break;
                case 1:
                    $content = db('article')->where(['id' => $contentId])->getRow();
                    break;
                case 2:
                    $content = db('topic')->where(['id' => $contentId])->getRow();
                    break;
                default:
                    $content = ['content' => '当前内容主体是用户，请查看补充项'];
                    break;
            }

            if (empty($content) || empty($content['id'])) {
                $this->jump(RESULT_ERROR, '举报失败,举报内容不存在');
            }

            $data                 = $this->param;
            $data['prosecutorId'] = $uid;
            $data['defendantId']  = $pid;
            $data['create_time']  = time();
            $data['report']       = $data['sendMsg'];
            $data['contentType']  = $data['type'];

            $result = self::$datalogic->setname('tipoffs')->dataAdd($data, false);
            $fs     = webconfig('auto_colse_content');

            if ($fs != 0 && $result[0] == 'success') {
                //如果加入判断基数，看是否自动屏蔽内容
                $counts = db('tipoffs')->where(
                    ['defendantId' => $pid,
                     'contentId'   => $contentId,
                     'contentType' => $data['type']])
                    ->count();
                if ($counts >= $fs) {
                    switch ($data['contentType']) {
                        case 0:
                            $info = self::$datalogic->setname('comment')->setDataValue(['id' => $data['contentId']], 'status', 0);
                            break;
                        case 1:
                            $info = self::$datalogic->setname('article')->setDataValue(['id' => $data['contentId']], 'status', 0);
                            break;
                        case 2:
                            $info = self::$datalogic->setname('topic')->setDataValue(['id' => $data['contentId']], 'status', 0);
                            break;
                        default:
                            $info = ['success'];
                            break;
                    }
                    if ($info[0] != 'success') {
                        $this->jump(RESULT_ERROR, '举报失败', '', ['data' => $info]);
                    }
                }
                $this->jump(RESULT_SUCCESS, '举报成功' . $counts);
            } else {
                $this->jump(RESULT_ERROR, '举报失败', '', ['data' => $result]);
            }
        } else {

            $this->jump(([RESULT_ERROR, '错误操作']));
        }

    }

}