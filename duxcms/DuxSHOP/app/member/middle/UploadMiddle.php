<?php
namespace app\member\middle;
/**
 * 会员上传
 */
class UploadMiddle extends \app\base\middle\BaseMiddle {

    protected $_model = 'member/MemberUser';

    protected function post() {
        $userId = $this->params['user_id'];
        $type = $this->params['type'] ? $this->params['type'] : 'jpg,png,bmp,jpeg';
        $width = $this->params['width'] ? $this->params['width'] : 1000;
        $height = $this->params['height'] ? $this->params['height'] : 1000;
        $config = array();
        $config['dir_name'] = 'u-' . $userId . '-' . date('Y-m-d');
        if ($type) {
            $config['upload_exts'] = $type;
        }
        if ($width && $height) {
            $config['thumb_width'] = $width;
            $config['thumb_height'] = $height;
            $config['thumb_status'] = true;
            $config['thumb_type'] = 1;
        }
        $fileInfo = target('base/Upload')->upload($config);
        if (!$fileInfo) {
            return $this->stop(target('base/Upload')->getError());
        }
        $list = [];
        if ($fileInfo['url']) {
            $list[] = $fileInfo;
        } else {
            $list = $fileInfo;
        }
        $files = [];
        foreach ($list as $key => $data) {
            $data['user_id'] = $userId;
            $data['time'] = time();
            if (!target('member/MemberFile')->add($data)) {
                return $this->stop('上传失败,请稍候再试!');
            }
            $files[$key] = $data;
        }
        if(empty($files)) {
            return $this->stop('上传失败,请稍候再试!');
        }
        return $this->run($files, '上传成功');
    }

}
