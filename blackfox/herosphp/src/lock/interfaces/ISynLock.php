<?php
/**
 * 同步锁接口
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2016-12-07 v2.0.0
 */
namespace herosphp\lock\interfaces;

interface ISynLock {

    /**
     * 尝试去获取锁，成功返回false并且一直阻塞
     * @throws \herosphp\exception\HeroException
     * @return bool
     */
    public function tryLock();

    /**
     * 释放锁
     * @throws \herosphp\exception\HeroException
     * @return bool
     */
    public function unlock();

} 