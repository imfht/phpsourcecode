<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\controller\ControllerBase;
use app\common\logic\File as LogicFile;
use Qiniu\json_decode;
use app\common\logic\Common as LogicCommon;

/**
 * 文件控制器
 */
class File extends ControllerBase
{

    // 文件逻辑
    private static $fileLogic = null;
    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();
        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class);
        self::$fileLogic   = get_sington_object('fileLogic', LogicFile::class);
    }

    /**
     * 图片上传
     */
    public function pictureUpload()
    {

        $result = self::$fileLogic->pictureUpload();

        return json($result);
    }

    /**
     * 文件上传
     */
    public function fileUpload()
    {

        $result = self::$fileLogic->fileUpload();

        return json($result);
    }

    public function getFileInfo()
    {

        $result = self::$fileLogic->getFileInfo(['id' => $this->param['id']]);

        return json($result);

    }


    public function downloadFile()
    {
        $fileid = decrypt($this->param['id']);

        $result = self::$fileLogic->getFileInfo(['id' => $fileid]);

        $id = decrypt($this->param['tid']);


        $uid = is_login();

        if ($uid == 0) {

            $this->error('请先登录', es_url('appstore/info', ['aid' => $id]));
        }

        $userinfo = self::$datalogic->setname('user')->getDataInfo(['id' => $uid]);

        $info = db('app')->where(['aid' => $id])->getRow();

        if ($info['fileid'] != $fileid) {

            $this->error('非法参数', es_url('appstore/info', ['aid' => $id]));
        }

        if ($info['uid'] != $uid) {
            //扣除积分

            if (db('point_note')->where(['uid' => $uid, 'itemid' => $id, 'controller' => 'downapp', 'scoretype' => 'point'])->count() > 0) {


            } else {


                if ($userinfo['point'] < $info['score']) {
                    $this->error('积分不足', es_url('appstore/info', ['aid' => $id]));

                } else {

                    point_change($uid, 'point', $info['score'], 2, 'downapp', $id, $info['uid']);

                    point_change($info['uid'], 'point', intval($info['score'] * webconfig('point_percent') / 100), 1, 'downapp', $id, $uid);

                }


            }


        }

        self::$datalogic->setname('app')->setIncOrDec(['aid' => $id], 'down', 1);

        homeaction_log($uid, 21, $fileid);


        $url = [PATH_FILE . $result['savepath']];

        self::$fileLogic->download($url, $result['name'], 1);


        /* 	if($userinfo['point']<$info['score']&&$info['uid']!=$userinfo['id']){
         $this->jump([RESULT_ERROR, '积分不足',url('appstore/info',array('aid'=>$id))]);
         }else{




         }
        */


    }
}
