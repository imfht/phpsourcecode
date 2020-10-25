<?php

namespace Bluehouseapp\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use  Bluehouseapp\Bundle\CoreBundle\Controller\Resource\ResourceController;
use  Bluehouseapp\Bundle\CoreBundle\Entity\Member;
use  Bluehouseapp\Bundle\CoreBundle\Form\MemberType;
use  Bluehouseapp\Bundle\CoreBundle\Form\MemberImageType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberController  extends ResourceController
{

    /**
     */
    public function listAction(Request $request)
    {
        $repo = $this->getRepository();
        $locked = $request->query->get('locked', 0);

        $results =$repo->queryUserByLockedPaginator($locked);

        $activeCount =$repo->countUserByLocked(false);
        $inactiveCount =$repo->countUserByLocked(true);


        $results->setCurrentPage($request->get('page', 1), true, true);
        $results->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('list.html'))
            ->setData(array(
                'entities'    => $results,
                'activeCount' => $activeCount,
                'inactiveCount' => $inactiveCount
            ))
        ;

        return $this->handleView($view);
    }


    /**
 */
    public function enableAction(Request $request,$id)
    {
        $member =$this->getRepository()
            ->find($id);

       $em = $this->getDoctrine()->getManager();
        $member->setLocked(false);

         $em->flush($member);
        return $this->redirect($this->generateUrl('bluehouseapp_members_list'));
    }

    /**
     */
    public function disableAction(Request $request,$id)
    {
        $member = $this->getRepository()->find($id);
        $em = $this->getDoctrine()->getManager();
        $member->setLocked(true);

       $em->flush($member);

        return $this->redirect($this->generateUrl('bluehouseapp_members_list'));
    }


    public function needAvatarImageAction(Request $request)
    {


        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('needAvatarImage.html'))
            ->setData(array())
        ;
        return $this->handleView($view);
    }

    public function editMemberAction(Request $request)
    {

        $current = $this->get('security.context')->getToken()->getUser();
        $member = $this->getRepository()->find($current->getId());
        if(!$member->getNickname()){
            $member->setNickname($member->getUsername());
        }



        $form = $this->createForm('bluehouseapp_member',$member,array(
            'action'=>$this->generateUrl('member_update'),
            'method'=>'POST'
        ));



        $memberImageForm= $this->getMemberImageForm($member);

        $param['member']=$member;
        $param['form']=$form->createView();
        $param['memberImageForm']=$memberImageForm->createView();

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('edit.html'))
            ->setData($param)
        ;
        return $this->handleView($view);
    }


    public function updateMemberImageAction(Request $request)
    {
        $current = $this->get('security.context')->getToken()->getUser();
        $member = $this->getRepository()->find($current->getId());

      //  $memberType = new MemberType();
        $form = $this->createForm('bluehouseapp_member',$member,array(
            'action'=>$this->generateUrl('member_update'),
            'method'=>'POST'
        ));

        $memberImageForm= $this->getMemberImageForm($member);

        $memberImageForm->handleRequest($request);
        if($memberImageForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            $member->setModified(new \DateTime());
            $em->persist($member);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','保存成功');
            return $this->redirect($this->generateUrl('member_edit'));
        }
        $param['member']=$member;
        $param['form']=$form->createView();
        $param['memberImageForm']=$memberImageForm->createView();
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('edit.html'))
            ->setData($param)
        ;
        return $this->handleView($view);
    }


    public  function  getMemberImageForm($member){
        $isEdit = $member->getAvatar()!='';
        $memberImageType = new MemberImageType($isEdit);
/*
        $memberImageForm = $this->createForm('bluehouseapp_member_image',$member,array(
            'action'=>$this->generateUrl('member_update_image'),
            'method'=>'POST'
        ),array('isEdit'=>$isEdit));
*/
        $memberImageForm = $this->createForm($memberImageType,$member,array(
            'action'=>$this->generateUrl('member_update_image'),
            'method'=>'POST'
        ));
    return $memberImageForm;
}

    /**
     *
     */
    public function updateMemberAction(Request $request)
    {
        $current = $this->get('security.context')->getToken()->getUser();
        $member = $this->getRepository()->find($current->getId());

        $memberImageForm= $this->getMemberImageForm($member);

        $form = $this->createForm('bluehouseapp_member',$member,array(
            'action'=>$this->generateUrl('member_update'),
            'method'=>'POST'
        ));

        $form->handleRequest($request);
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $member->setModified(new \DateTime());
            $em->persist($member);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','保存成功');
            return $this->redirect($this->generateUrl('member_edit'));
        }
        $param['member']=$member;
        $param['form']=$form->createView();
        $param['memberImageForm']=$memberImageForm->createView();

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('edit.html'))
            ->setData($param)
        ;
        return $this->handleView($view);
    }

    /**
     * Finds and displays a member entity.
     */
    public function showMemberAction(Request $request, $username)
    {
        $param=array();
        $member=$this->getRepository()->findOneBy(array('username'=>$username,'locked'=>false));

        if (!$member) {
            throw new NotFoundHttpException('这个用户不存在');
        }

        $posts=$this->getPaginationTopics($member);
        $posts->setCurrentPage($request->get('page', 1), true, true);
        $posts->setMaxPerPage($this->config->getPaginationMaxPerPage());


        $lastComments = array();
            foreach ($posts  as $post){
                $lastComments[$post->getId()]=$this->get('bluehouseapp.repository.postcomment')->getLastComment($post);

            }

        $postComments=$this->getPaginationReplies($member);

        $postComments->setCurrentPage($request->get('page', 1), true, true);
        $postComments->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $param['member'] = $member;
            $param['posts'] = $posts;
            $param['lastComments'] = $lastComments;
            $param['postComments'] = $postComments;
            //  return $param;

            $view = $this
                ->view()
                ->setTemplate($this->config->getTemplate('show.html'))
                ->setData($param)
            ;
            return $this->handleView($view);
       // }


    }

    public function showMemberTopicsAction(Request $request, $username)
    {
        $param=array();
        $member=$this->getRepository()->findOneBy(array('username'=>$username,'locked'=>false));

        if (!$member) {
            throw new NotFoundHttpException('这个用户不存在');
        }
        $posts=$this->getPaginationTopics($member);
        $posts->setCurrentPage($request->get('page', 1), true, true);
        $posts->setMaxPerPage($this->config->getPaginationMaxPerPage());


        $lastComments = array();
        foreach ($posts  as $post){
            $lastComments[$post->getId()]=$this->get('bluehouseapp.repository.postcomment')->getLastComment($post);

        }
        $param['member'] = $member;
        $param['posts'] = $posts;
        $param['lastComments'] = $lastComments;
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('showTopics.html'))
            ->setData($param)
        ;
        return $this->handleView($view);
    }

    public function showMemberRepliesAction(Request $request, $username)
    {
        $param=array();
        $member=$this->getRepository()->findOneBy(array('username'=>$username,'locked'=>false));

        if (!$member) {
            throw new NotFoundHttpException('这个用户不存在');
        }

        $postComments=$this->getPaginationReplies($member);

        $postComments->setCurrentPage($request->get('page', 1), true, true);
        $postComments->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $param['member'] = $member;
        $param['postComments'] = $postComments;
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('showReplies.html'))
            ->setData($param)
        ;
        return $this->handleView($view);

    }
    public  function  getPaginationTopics( $member){
        $repository = $this->get('bluehouseapp.repository.post');
        $resources = $this->resourceResolver->getResource(
            $repository,
            'getPostsByMember',
            array($member)
        );
        return $resources;
    }
    public  function  getPaginationReplies( $member){
        $repository = $this->get('bluehouseapp.repository.postcomment');

        $postComments = $this->resourceResolver->getResource(
            $repository,
            'getPostCommentsByMember',
            array($member)
        );
    return $postComments;
    }
} 