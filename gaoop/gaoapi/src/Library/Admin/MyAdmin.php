<?php


namespace App\Library\Admin;


use App\Library\Helper\GeneralHelper;
use Sonata\AdminBundle\Admin\AbstractAdmin;

class MyAdmin extends AbstractAdmin
{
    /**
     * User: gao
     * Date: 2019/11/29
     * Description: 多项目权限条码编辑校验
     * @param $entity_class
     * @param array $idx
     * @param bool $allElements
     */
    public function verifyInfo($entity_class, $idx = [], $allElements = false)
    {
        // 如执行全部批处理操作，则不做校验，因为createQuery()已添加info关联
        if ($allElements) {
            return;
        }

        $objs = $this->modelManager->findBy($entity_class, ['id' => $idx, 'infoId' => GeneralHelper::getInstance()->info_id]);
        if (count($objs) != count($idx)) {
            GeneralHelper::getOneInstance()->addFlash('sonata_flash_error', '无权限操作该对象');
            $url = $this->generateUrl('list', $this->getFilterParameters());

            header("Location:" . $url);
            exit();
        }
    }

}