<?php
/**
 * 配置文件开启了表单令牌验证 防止表单重复提交
 */
return array(
     // 添加下面一行定义即可
     //'view_filter' => array('Behavior\TokenBuild'),
    // 如果是3.2.1以上版本 需要改成
    'view_filter' => array('Behavior\TokenBuildBehavior'),
);

?>
