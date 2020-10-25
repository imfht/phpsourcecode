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
use Symfony\Component\HttpFoundation\Response;
/**
 * PostComment controller.
 *
 */
class PostCommentController extends ResourceController
{

/*
    public function showAction(Request $request, $id)
    {
        $param=array();
        $query = $this->getDoctrine()->getManager()
            ->getRepository('BluehouseappCoreBundle:PostComment')
            ->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andWhere('c.status = :status')
            ->setParameters(array(':id' => $id,'status'=>true))
            ->orderBy('c.id', 'asc')
            ->getQuery();
        try {
            $entity =  $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $entity = null;
        }

        if (!$entity) {
            throw new NotFoundHttpException('这个评论不存在');
        }

        $wh_content=$request->headers->get('WH-CONTEXT');
      if($wh_content==''||$wh_content==null){
           $param = array(
              'entity' => $entity
          );
          return $param;
       }else
        {
            $data = array(
                'data' => $entity
            );
            $serializer = $this->get('jms_serializer');
            $response = new Response($serializer->serialize($data, 'json'));
            $response->headers->set('Content-Type', 'text/html;; charset=utf-8');


            return $response;
        }

    }
*/
}
