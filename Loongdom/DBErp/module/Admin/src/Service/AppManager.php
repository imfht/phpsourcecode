<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\Service;

use Admin\Entity\App;
use Doctrine\ORM\EntityManager;

class AppManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加应用
     * @param array $data
     * @return App
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addApp(array $data)
    {
        $app = new App();
        $data['appId']      = null;
        $data['appAddTime'] = time();
        $app->valuesSet($data);

        $this->entityManager->persist($app);
        $this->entityManager->flush();

        return $app;
    }

    /**
     * 更新应用信息
     * @param App $app
     * @param $data
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateApp(App $app, $data)
    {
        $app->valuesSet($data);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 删除应用
     * @param App $app
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteApp(App $app)
    {
        $this->entityManager->remove($app);
        $this->entityManager->flush();
    }
}