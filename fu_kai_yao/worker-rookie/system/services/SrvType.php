<?php
namespace system\services;
/**
 * 业务类型定义，用于定义业务类型名，命名规则：点号隔开
 * 格式说明：
 *      const 模块名_[子目录_]service对象单词名 = '模块名.[子目录_]service对象名';
 *
 * 例子:
 *      const COMMON_TEST    = 'common.test.Test';
 *
 * @author fukaiyao 2020-1-3
 *
 */
class SrvType
{
    /**
     * 测试业务
     */
    const COMMON_TEST = 'common.test.Test';
}
