<?php


namespace App\Controller\Backend;


use App\Entity\AdminUser;
use App\Entity\Log;
use App\Library\Controller\MyController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InfoAdminController extends MyController
{
    /**
     * User: Gao
     * Date: 2020/2/21
     * Description: 日志
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function logAction(Request $request, $id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $page = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit', 10);
        $version = $request->get('version', null);
        $path = $request->get('path', null);
        $action = $request->get('action', null);
        if ($version != '') {
            $pagination = $this->getDoctrine()->getRepository(Log::class)->getSingleVersionList($id, $version, $path, $action, $page, $limit);
        } else {
            $pagination = $this->getDoctrine()->getRepository(Log::class)->getVersionList($id, $version, $path, $action, $page, $limit);
        }

        $fields = $this->admin->getShow();
        $admin_users = $this->getDoctrine()->getRepository(AdminUser::class)->getAdminUserArray();
        return $this->renderWithExtraParams('Backend/info/log.html.twig', [
            'action' => 'log',
            'object' => $object,
            'elements' => $fields,
            'pagination' => $pagination,
            'admin_users' => $admin_users,
            'filter' => [
                'version' => $version,
                'path' => $path,
                'action' => $action
            ]
        ]);
    }
}