<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use Presta\ImageBundle\Form\Type\ImageType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AdminUserAdminController extends CRUDController
{
    /**
     * User: Gao
     * Date: 2020/3/26
     * Description: 个人中心
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $admin_user = $this->getUser();
        if (!is_object($admin_user)) {
            throw new AccessDeniedException('用户未登录');
        }

        $form = $this->createFormBuilder($admin_user)
            ->add('email', null, ['attr' => ['class' => 'form-control']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => '密码不一致',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options' => ['label' => 'Password', 'attr' => ['class' => 'form-control'], 'help' => '留空则不更新密码，密码应包含数字和字母，长度6~20'],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['class' => 'form-control']]
            ])
            ->add('save', SubmitType::class, ['label' => '  更新  ', 'attr' => ['class' => 'btn btn-success']])
            ->add('avatar_file', ImageType::class, [
                'enable_remote' => false,
                'max_width' => 800,
                'max_height' => 800,
                'preview_width' => '300px',
                'preview_height' => '300px',
                'required' => false,
                'delete_label' => '删除图片',
                'help' => '图片格式：jpg、png'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // 如果更新了密码，则加密处理
                $new_password = $form->get('password')->getData();
                if (!is_null($new_password)) {
                    $admin_user->setPassword($passwordEncoder->encodePassword($admin_user, $new_password));
                }
                $this->getDoctrine()->getManager()->persist($admin_user);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('sonata_flash_success', '数据已被成功更新');
                return $this->redirectToRoute('admin_app_adminuser_profile');
            } else {
                $this->addFlash('sonata_flash_error', '更新时有错误发生');
            }
        }

        return $this->renderWithExtraParams('Backend/admin_user/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
