<?php
/**
 * 同步锁工厂类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2016-12-07 v2.0.0
 */
namespace herosphp\lock;

use herosphp\lock\interfaces\ISynLock;

class SynLockFactory {

    private static $_FILELOCK_POOL = array(); //文件锁池

    /**
     * 获取文件锁
     * @param $key
     * @return ISynLock
     */
    public static function getFileSynLock($key) {

        if ( !isset(self::$_FILELOCK_POOL[$key])  ) {
            self::$_FILELOCK_POOL[$key] = new FileSynLock($key);
        }
        return self::$_FILELOCK_POOL[$key];
    }

} 