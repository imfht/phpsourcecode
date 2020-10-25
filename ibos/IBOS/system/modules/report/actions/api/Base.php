<?php
/**
 * 基础控制器方法类
 */

namespace application\modules\report\actions\api;

use application\core\model\Log;
use application\core\utils\Ibos;
use application\modules\report\utils\Report as ReportUtil;

class Base extends \CAction
{

    //我接收到的
    const RECEIVE = "receive";

    //我发送的
    const SEND = "send";

    //未读
    const UNREAD = "unread";

    /**
     * 使用yii的get方法获取并且使用规则验证表单字段
     * @staticvar array $data 前端数据
     * @return ajaxReturn
     */
    public function getData()
    {
        static $data = null;
        if (null === $data) {
            $body = Ibos::app()->getRequest()->getRawBody();
            $bodyArray = \CJSON::decode($body);
        }
        return $bodyArray;
    }
    /**
     * 事件记录日志
     * @param string $id 联系人等id
     */
    public function log($id)
    {
        $log = array(
            'user' => Ibos::app()->user->uid,
            'ip' => Ibos::app()->setting->get('clientip'),
            'isSuccess' => !!$id
        );
        Log::write($log);
    }

    /**
     * 获得模板中的所有图标，图标都是放在report/static/image/tmpl_icon文件夹中，
     * 最终的结果返回该文件下的所有图标的图标名，设计说这个需求以后不改的
     */
    public function getPictutre()
    {
        $path = PATH_ROOT . str_replace('\\', DIRECTORY_SEPARATOR, '\modules\report\static\image\tmpl_icon');
        $filenameList = array();
        if (is_dir($path)){
            $filenames = scandir($path);
            if (!empty($filenames)){
                foreach ($filenames as $filename){
                    if ($filename != "." && $filename != ".."){
                        $filenameList[] = $filename;
                    }
                }
            }
        }
        return $filenameList;
    }

    /**
     * 获取总结模块后台设置
     * @return array
     */
    public function getReportConfig() {
        return ReportUtil::getSetting();
    }

    /**
     * 获取自动评阅的图章id
     * @return integer
     */
    protected function getAutoReviewStamp() {
        $config = $this->getReportConfig();
        return intval( $config['autoreviewstamp'] );
    }

    /**
     * 检查是否开启图章功能
     */
    protected function issetStamp()
    {
        $config = $this->getReportConfig();
        return !!$config['stampenable'];
    }
}