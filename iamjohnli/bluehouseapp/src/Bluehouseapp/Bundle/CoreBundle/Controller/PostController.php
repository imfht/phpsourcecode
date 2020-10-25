<?php

namespace Bluehouseapp\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use  Bluehouseapp\Bundle\CoreBundle\Form\PostType;
use  Bluehouseapp\Bundle\CoreBundle\Entity\Post;
use  Bluehouseapp\Bundle\CoreBundle\Entity\PostComment;
use  Bluehouseapp\Bundle\CoreBundle\Entity\Audit;
use  Bluehouseapp\Bundle\CoreBundle\Form\PostCommentType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use  Bluehouseapp\Bundle\CoreBundle\Controller\Resource\ResourceController;
/**
 * Post controller.
 *
 */
class PostController extends ResourceController
{

    public function listAction(Request $request)
    {
        return $this->redirect($this->generateUrl('post_by_category', array('currentCategoryId'=>'0')));
    }

    public function deletePostAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $post =$this->get('bluehouseapp.repository.post')->getPost($id);
        if ($post) {
            if (!$post) {
                throw new NotFoundHttpException('Unable to find Post entity.');
            }
            $post->setModified(new \DateTime());
            $post->setStatus(false);
            //  $em->remove($post);
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add('success', '删除成功');
        return $this->redirect($this->generateUrl('post'));

    }


    public function deletePostCommentAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $postComment =$this->get('bluehouseapp.repository.postcomment')->find($id);
        if($postComment){
            // $em->remove($postComment);
            $postComment->setModified(new \DateTime());
            $postComment->setStatus(false);
            $em->flush();
            $em->persist($postComment->getPost());
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add('success','删除成功');
        return $this->redirect($this->generateUrl('post_show', array('id' => $postComment->getPost()->getId())));
    }



    public function listPostsByNodeAction(Request $request,$currentNodeId=0)
    {

        $categories = $this->get('bluehouseapp.repository.category')->getAllEnableCategories();
        $currentCategory = null;
        $currentNode = null;
        if($currentNodeId==0){
            if (count($categories) > 0) {
                $currentCategory = $categories[0];
                $currentCategoryId=$currentCategory->getId();
            }
        }else{
            $currentNode =  $this->get('bluehouseapp.repository.node')->getNode($currentNodeId);

            if (!$currentNode) {
                throw new NotFoundHttpException('此节点不存在.');
            }
        }
        if($currentCategory!=null){
            $nodes = $currentCategory->getNodes();
            foreach ($nodes as $node) {
                if($node->getStatus() and $node->getEnabled()){
                    $currentNode=$node;
                    break;
                }
            }
        }

        $entities= $this->get('bluehouseapp.repository.post')->getPostByNode($currentNodeId);
        $entities->setCurrentPage($request->get('page', 1), true, true);
        $entities->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $lastComments = array();
            foreach ($entities as $entity) {
                $lastComments[$entity->getId()] = $this->get('bluehouseapp.repository.postcomment')->getLastComment($entity);
            }

        $node= $this->get('bluehouseapp.repository.node')->getNode($currentNodeId);

       // $postCounts= $this->get('bluehouseapp.repository.post')->countPostsByNode($node);


        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('postsByNode.html'))
            ->setData(array(
                'entities' => $entities,
                'categories' => $categories,
                'lastComments' => $lastComments,
                'currentCategory' => $currentCategory,
                'currentNode' => $currentNode,
               // 'postCounts'=>$postCounts
            ))
        ;
        return $this->handleView($view);
    }


    public function listPostsByCategoryAction(Request $request,$currentCategoryId=0)
    {

        $categories = $this->get('bluehouseapp.repository.category')->getAllEnableCategories();

        $currentCategory = null;
        $currentNode = null;
        if($currentCategoryId==0){
        if (count($categories) > 0) {
            $currentCategory = $categories[0];
            $currentCategoryId=$currentCategory->getId();
        }
        }else{
            foreach ($categories as $category) {
                if($currentCategoryId==$category->getId()){
                    $currentCategory=$category;
                    break;
                }
            }
            if (!$currentCategory) {
                throw new NotFoundHttpException('此分类不存在.');
            }

        }
    if($currentCategory!=null){
        $nodes = $currentCategory->getNodes();

        if (count($nodes) > 0) {
            $currentNode = $nodes[0];
        }
    }
        $entities= $this->get('bluehouseapp.repository.post')->getPostByCategory($currentCategory);
        $entities->setCurrentPage($request->get('page', 1), true, true);
       $entities->setMaxPerPage($this->config->getPaginationMaxPerPage());


            $lastComments = array();
            foreach ($entities as $entity) {
                $lastComments[$entity->getId()] = $this->get('bluehouseapp.repository.postcomment')->getLastComment($entity);
            }



        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setData(array(
                'entities' => $entities,
                'categories' => $categories,
                'lastComments' => $lastComments,
                'currentCategory' => $currentCategory,
                'currentNode' => $currentNode
            ))
        ;
        return $this->handleView($view);
    }


    public function createPostAction(Request $request,$nodeId)
    {
        $entity = new Post();
        $form = $this->createCreateForm($entity,$nodeId);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $current = $this->get('security.context')->getToken()->getUser();
            $entity->setMember($current);
            $em = $this->getDoctrine()->getManager();
            $node= $this->get('bluehouseapp.repository.node')->getNode($nodeId);

            $entity->setNode($node);
          //  $em->persist($entity);
           // $em->flush();


            $this->domainManager->create($entity);


            $audit = new Audit();
            $audit->setEntityId($entity->getId());
            $audit->setName($entity->getTitle().'|'.'by user:'.$current->getUsername().'('.$current->getNickname().')');
            $audit->setContent($entity->getContent());
            $audit->setType('post');
            $em->persist($audit);
            $em->flush();

            return $this->redirect($this->generateUrl('post_by_category',array('currentCategoryId' => $entity->getNode()->getCategory()->getId())));
        }
        return $this->render('BluehouseappWebBundle:Frontend/Post:new.html.twig',
               array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Post $entity,$currentNodeId)
    {
        //new PostType()
        $form = $this->createForm('bluehouseapp_post', $entity, array(
            'action' => $this->generateUrl('post_create',array('nodeId' =>$currentNodeId)),
            'method' => 'POST',
        ));
        return $form;
    }


    public function newPostAction(Request $request,$nodeId)
    {
      //  $nodeId = $request->query->get('nodeId', 0);

        $currentNode= $this->get('bluehouseapp.repository.node')->getNode($nodeId);
        $entity = new Post();
        $entity->setNode($currentNode);

        $nodes=$currentNode->getCategory()->getNodes();

        $form = $this->createCreateForm($entity,$nodeId);
        $current = $this->get('security.context')->getToken()->getUser();
        $member = $this->get('bluehouseapp.repository.member')->find($current->getId());

        if ($member->getAvatar()!=''&&$member->getAvatar()!=null)
        {
            return $this->render('BluehouseappWebBundle:Frontend/Post:new.html.twig',
             array(
                'entity' => $entity,
                'form' => $form->createView(),
                 'node'=>$currentNode
            ));
        }
        else {
            return $this->redirect($this->generateUrl('member_needAvatarImage', array()));
        }
    }


    public function  showPostAction(Request $request, $id)
    {
        $param = array();

        $post =$this->get('bluehouseapp.repository.post')->getPost($id);

        if (!$post || !$post->getStatus()) {
            throw new NotFoundHttpException('这个帖子不存在');
        }
        $param['entity'] = $post;

        $comments = $this->getComments($post, $request);
        $param['comments'] = $comments;

        $comment = new PostComment();
        $form = $this->getCommentForm($post, $comment);
        $param['form'] = $form->createView();


        return $this->render('BluehouseappWebBundle:Frontend/Post:show.html.twig',
            $param);

    }


    public function commentCreateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $post =$this->get('bluehouseapp.repository.post')->getPost($id);
        if (!$post || !$post->getStatus()) {
            throw $this->createNotFoundException("这个帖子不存在");
        }
        $param['entity'] = $post;

        $comment = new PostComment();
        $form = $this->getCommentForm($post, $comment);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $current = $this->get('security.context')->getToken()->getUser();
            $comment->setPost($post);
            $comment->setMember($current);

            $em->persist($comment);
            $em->flush();
            $post = $comment->getPost();
            $post->setLastCommentTime(new \DateTime());
            $em->persist($post);
            $em->flush();

            $audit = new Audit();
            $audit->setEntityId($comment->getId());
            $audit->setName('by user:'.$current->getUsername().'('.$current->getNickname().')');
            $audit->setContent($comment->getContent());
            $audit->setType('post_comment');
            $em->persist($audit);
            $em->flush();
            return $this->redirect($this->generateUrl('post_show', array('id' => $post->getId())));
        }

        $param['form'] = $form->createView();
        $comments = $this->getComments($post,$request);
        $param['comments'] = $comments;

        return $this->render('BluehouseappWebBundle:Frontend/Post:show.html.twig',
            $param);

    }

    private function getCommentForm($post, $comment)
    {
      //  $commentType = new PostCommentType();
        $form = $this->createForm('bluehouseapp_postComment', $comment, array(
            'action' => $this->generateUrl('post_comment_create', array('id' => $post->getId())),
            'method' => 'POST'
        ));
        return $form;
    }

    private function getComments($post,$request)
    {
        $comments= $this->get('bluehouseapp.repository.postcomment')->getCommentsByPost($post);
        $comments->setCurrentPage($request->get('page', 1), true, true);
        $comments->setMaxPerPage($this->config->getPaginationMaxPerPage());
        return $comments;
    }
}
