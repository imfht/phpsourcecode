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

namespace Admin\Controller;

use Admin\Data\Common;
use Admin\Entity\App;
use Admin\Form\AppForm;
use Admin\Service\AppManager;
use Doctrine\ORM\EntityManager;
use Shop\Entity\ShopOrder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class AppController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $appManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        AppManager      $appManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->appManager       = $appManager;
    }

    /**
     * 应用列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $appList = $this->entityManager->getRepository(App::class)->findAll();
        return ['appList' => $appList];
    }

    /**
     * 添加应用
     * @return array|\Zend\Http\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addAction()
    {
        $form = new AppForm();
        $form->get('appType')->setValueOptions(Common::appType($this->translator));
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();
                $this->appManager->addApp($data);

                $message = sprintf($this->translator->translate('应用 %s 添加成功！'), $data['appName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商城绑定'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('app');
            }
        }

        return ['form' => $form];
    }

    /**
     * 编辑应用信息
     * @return array|\Zend\Http\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction()
    {
        $appId = (int) $this->params()->fromRoute('id', -1);
        $appInfo = $this->entityManager->getRepository(App::class)->findOneByAppId($appId);
        if($appInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该应用不存在！'));
            return $this->redirect()->toRoute('app');
        }

        $form = new AppForm();
        $form->get('appType')->setValueOptions(Common::appType($this->translator));
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();
                $this->appManager->updateApp($appInfo, $data);

                $message = sprintf($this->translator->translate('应用 %s 编辑成功！'), $data['appName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商城绑定'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('app');
            }
        }

        $form->setData($appInfo->valuesArray());

        return ['appInfo' => $appInfo, 'form' => $form];
    }

    /**
     * 删除应用
     * @return \Zend\Http\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->redirect()->toRoute('app');

        $appId = (int) $this->params()->fromRoute('id', -1);
        $appInfo = $this->entityManager->getRepository(App::class)->findOneByAppId($appId);
        if($appInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该应用不存在！'));
            return $this->redirect()->toRoute('app');
        }

        $shopOrder = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['appId' => $appId]);
        if($shopOrder) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('应用下存在订单！'));
            return $this->redirect()->toRoute('app');
        }

        $this->appManager->deleteApp($appInfo);

        $message = sprintf($this->translator->translate('应用 %s 删除成功！'), $appInfo->getAppName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('商城绑定'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->redirect()->toRoute('app');
    }

    /**
     * 创建 accessId
     * @return JsonModel
     */
    public function createAccessIdAction()
    {
        $accessId = $this->adminSession('admin_id') . time() . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        return new JsonModel(['content' => $accessId]);
    }

    /**
     * 创建 accessSecret
     * @return JsonModel
     */
    public function createAccessSecretAction()
    {
        $chars        = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        $accessSecret = time() . $chars[rand(0, 25)] . str_replace('.', '',microtime(true));

        return new JsonModel(['content' => md5($accessSecret)]);
    }
}