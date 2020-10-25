<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright Copyright (c) 2012-2019 DBShop.net Inc. (http://www.dberp.net)
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    北京珑大钜商科技有限公司
 *
 */

namespace Admin\Controller;

use Admin\Entity\System;
use Admin\Form\SystemForm;
use Admin\Service\SystemManager;
use Doctrine\ORM\EntityManager;
use Zend\Crypt\BlockCipher;
use Zend\Http\Client;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class SystemController extends AbstractActionController
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var entityManager
     */
    private $entityManager;

    private $systemManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        SystemManager $systemManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->systemManager    = $systemManager;
    }

    /**
     * 系统设置
     * @return array|\Zend\Http\Response|\Zend\View\Model\ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction()
    {
        $form = new SystemForm();

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();
                foreach ($data as $dataKey => $dataValue) {
                    $where    = explode('|', $dataKey);
                    $oneSystem= $this->entityManager->getRepository(System::class)->findOneBy(['sysName' => $where[0], 'sysType' => $where[1]]);
                    if($oneSystem && $oneSystem->getSysBody() != $dataValue) $this->systemManager->editSystem($oneSystem, $dataValue);
                }

                $message = $this->translator->translate('系统设置 修改成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('系统设置'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('admin-system');
            }
        }

        $system = $this->entityManager->getRepository(System::class)->findAll();
        $systemArray = [];
        if($system) {
            foreach ($system as $value) {
                $systemArray[$value->getSysName().'|'.$value->getSysType()] = $value->getSysBody();
            }
            $form->setData($systemArray);
        }

        return ['form' => $form];
    }

}