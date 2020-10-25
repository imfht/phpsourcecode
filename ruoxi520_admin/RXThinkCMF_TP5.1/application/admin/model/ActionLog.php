<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\BaseModel;

/**
 * 行为日志-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class ActionLog
 * @package app\admin\model
 */
class ActionLog extends BaseModel
{
    // 设置数据表
    protected $table = null;
    // 自定义日志标题
    protected static $title = '';
    // 自定义日志内容
    protected static $content = '';

    /**
     * 初始化
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        // 设置表名
        $this->table = DB_PREFIX . 'action_log_' . date('Y') . '_' . date('m');
        // 初始化行为日志表
        $this->initTable();
    }

    /**
     * 初始化行为日志表
     * @return |null
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function initTable()
    {
        $tbl = $this->table;
        if (!$this->tableExists($this->table)) {
            $sql = "CREATE TABLE `{$tbl}` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '唯一性标识',
                  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为ID',
                  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否后台操作：1是 2否',
                  `username` varchar(60) CHARACTER SET utf8mb4 NOT NULL COMMENT '操作人用户名',
                  `method` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '请求类型',
                  `module` varchar(30) NOT NULL COMMENT '模型',
                  `action` varchar(255) NOT NULL COMMENT '操作方法',
                  `url` varchar(200) CHARACTER SET utf8mb4 NOT NULL COMMENT '操作页面',
                  `param` text CHARACTER SET utf8mb4 NOT NULL COMMENT '请求参数(JSON格式)',
                  `title` varchar(100) NOT NULL COMMENT '日志标题',
                  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT '内容',
                  `ip` varchar(18) CHARACTER SET utf8mb4 NOT NULL COMMENT 'IP地址',
                  `user_agent` varchar(360) CHARACTER SET utf8mb4 NOT NULL COMMENT 'User-Agent',
                  `create_user` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加人',
                  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
                  `mark` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '有效标识：1正常 0删除',
                  PRIMARY KEY (`id`) USING BTREE
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='系统行为日志表';";
            $this->query($sql);
        }
        return $tbl;
    }

    /**
     * 设置日志标题
     * @param $title 标题
     * @since 2020/7/10
     * @author 牧羊人
     */
    public static function setTitle($title)
    {
        self::$title = $title;
    }

    /**
     * 设置日志内容
     * @param $content 内容
     * @since 2020/7/10
     * @author 牧羊人
     */
    public static function setContent($content)
    {
        self::$content = $content;
    }

    /**
     * 创建行为日志
     * @author 牧羊人
     * @since 2020/7/10
     */
    public static function record()
    {
        // 日志数据
        $data = [
            'username' => '相约在冬季',
            'module' => request()->module(),
            'action' => request()->url(),
            'method' => request()->method(),
            'url' => request()->url(true), // 获取完成URL
            'param' => request()->param() ? json_encode(request()->param()) : '',
            'title' => self::$title ? self::$title : '操作日志',
            'content' => self::$content,
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
            'create_user' => get_uid(),
            'create_time' => time(),
        ];
        // 日志入库
        self::insert($data);
    }
}
