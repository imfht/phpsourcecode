<?php
/**
 * 模块接口
 * interface IModule
 */
interface IModule {
    /**
     * 安装时调用
     * @param $db
     * @return boolean
     */
    public function install(CDbConnection $db);

    /**
     * 删除时调用
     * @param $db
     * @return boolean
     */
    public function uninstall(CDbConnection $db);
}