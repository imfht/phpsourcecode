<?php

namespace App\Controller\Backend;

use App\Entity\AdminUserGroup;
use App\Entity\Info;
use App\Service\Redis;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends AbstractController
{
    /**
     * User: gao
     * Date: 2019/11/28
     * Description: 全局info选择器
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/general/info", name="admin_general_info")
     */
    public function choiceInfo()
    {
        $infos = $this->getDoctrine()->getRepository(Info::class)->findAll();
        return $this->render('Backend/default/choice_info.html.twig', [
            'infos' => $infos,
        ]);
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 管理模块
     * @param $sonata_admin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function AdminModule($sonata_admin)
    {
        $admin_user = $this->getUser();
        if (!is_object($admin_user)) {
            throw new AccessDeniedException('未登录，无权限访问');
        }

        $admin_modules = [];
        $admin_user_group = $this->getDoctrine()->getRepository(AdminUserGroup::class)->find($admin_user->getAdminUserGroupId());
        if (is_object($admin_user_group)) {
            // 获取可访问的模块
            $admin_modules = $this->getDoctrine()->getRepository(AdminUserGroup::class)->getAdminModuleByAdminUserGroup($admin_user_group);
        }

        return $this->render('Backend/default/admin_module.html.twig', [
            'admin_modules' => $admin_modules,
            'sonata_admin' => $sonata_admin
        ]);
    }

    /**
     * User: Gao
     * Date: 2020/3/28
     * Description: 控制面板
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function DashboardModule()
    {
        $admin_user = $this->getUser();
        if (!is_object($admin_user)) {
            throw new AccessDeniedException('未登录，无权限访问');
        }

        $admin_modules = [];
        $admin_user_group = $this->getDoctrine()->getRepository(AdminUserGroup::class)->find($admin_user->getAdminUserGroupId());
        if (is_object($admin_user_group)) {
            // 获取可访问的模块
            $admin_modules = $this->getDoctrine()->getRepository(AdminUserGroup::class)->getAdminModuleByAdminUserGroup($admin_user_group);
        }

        return $this->render('Backend/default/dashboard_module.html.twig', [
            'admin_modules' => $admin_modules,
        ]);
    }

    /**
     * User: gao
     * Date: 2020/3/20
     * Description: 用户信息区块
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function AdminUserInfo()
    {
        return $this->render('Backend/default/admin_user_info.html.twig', []);
    }

    /**
     * User: gao
     * Date: 2019/11/28
     * Description: ~
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/admin/general/info/set", name="admin_general_info_set")
     */
    public function setCurrentInfo(Request $request, Redis $redis)
    {
        $result = [
            'success' => false,
            'message' => ''
        ];

        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {
            $info_id = $request->get('info_id', 0);
            $info = $this->getDoctrine()->getRepository(Info::class)->find($info_id);
            if (is_object($info)) {
                $this->getDoctrine()->getManager()->createQuery('update App\Entity\Info p set p.isCurrent = 0')->execute();
                $info->setIsCurrent(true);
                $this->getDoctrine()->getManager()->persist($info);
                $this->getDoctrine()->getManager()->flush();

                $redis->setex(Info::REDIS_CURRENT_INFO_KEY, 600, serialize($info));

                $result['success'] = true;
            } else {
                $result['message'] = '所选Info不存在';
            }
        } else {
            $result['message'] = '未被允许的请求方式';
        }

        return $this->json($result);
    }

    /**
     * User: gao
     * Date: 2019/11/29
     * Description: 更新当前API文档
     * @param KernelInterface $kernel
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/admin/general/apidoc/update", name="admin_general_apidoc_update")
     */
    public function updateApiDoc(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'build:openapi-config-data'
        ]);
        $output = new BufferedOutput();
        $exitCode = $application->run($input, $output);
        if ($exitCode == 0) {
            return $this->json(['success' => true]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * User: gao
     * Date: 2019/11/29
     * Description: ~
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/general/info/links", name="admin_general_info_links")
     */
    public function showInfoLink()
    {
        $infos = $this->getDoctrine()->getRepository(Info::class)->findAll();
        return $this->render('Backend/default/show_info_link.html.twig', [
            'infos' => $infos,
        ]);
    }
}
