<?php
namespace app\system\service;
/**
 * 通知接口
 */
class NoticeService {

    /**
     * 新增通知
     * @param $content string 内容
     * @param $icon string  图标(Font Awesome)
     * @param $url string 详情连接
     * @param $type string 选项(primary,secondary,success,warning,danger)
     */
    public function addNotice($content, $icon = 'bell', $url = '', $type = 'primary') {
        return target('system/Notice')->add([
            'content' => $content,
            'icon' => $icon,
            'url' => $url,
            'type' => $type
        ]);
    }

}
