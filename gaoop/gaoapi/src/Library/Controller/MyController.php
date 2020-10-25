<?php


namespace App\Library\Controller;


use App\Library\Helper\GeneralHelper;
use Sonata\AdminBundle\Controller\CRUDController;

class MyController extends CRUDController
{
    /**
     * User: gao
     * Date: 2019/12/4
     * Description: 操作权限验证
     * @param $object
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
     */
    public function verify($object)
    {
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object'));
        }

        if ($object->getInfoId() != GeneralHelper::getInstance()->info_id) {
            $this->addFlash('sonata_flash_error', '无权限操作该对象');
            return $this->redirectToList();
        }

        return null;
    }
}