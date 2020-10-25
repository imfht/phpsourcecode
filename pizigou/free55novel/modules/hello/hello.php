<?php
/**
 * 模块安装文件：必须
 * 命名规则：模块名首之母大写 + Module
 * 文件名规则：模块名 + ".php"
 */
class HelloModule implements IModule
{
    /**
     * 安装时调用，传入参数为数据库对象，数据库对象请参考 Yii CDbConnection
     * 安装成功返回true 否则返回false*
     * @param CDbConnection $db
     * @return bool
     */
    public function install(CDbConnection $db)
    {
        return true;
    }

    /**
     * 卸载时调用，传入参数为数据库对象，数据库对象请参考 Yii CDbConnection
     * 卸载成功返回true 否则返回false
     * @param CDbConnection $db
     * @return bool
     */
    public function uninstall(CDbConnection $db)
    {
        return true;
    }
}