<?php

namespace Bluehouseapp\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use  Bluehouseapp\Bundle\CoreBundle\Entity\Node;
use  Bluehouseapp\Bundle\CoreBundle\Form\NodeType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use  Bluehouseapp\Bundle\CoreBundle\Controller\Resource\ResourceController;
/**
 * Node controller.
 *
 */
class NodeController extends ResourceController
{

    /**
     */
    public function enableAction(Request $request,$id)
    {
        $node = $this->getRepository()
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $node->setEnabled(true);
        $em->flush($node);
        return $this->redirect($this->generateUrl('bluehouseapp_node_index'));
    }

    /**
     */
    public function disableAction(Request $request,$id)
    {
        $node = $this->getRepository()
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $node->setEnabled(false);
        $em->flush($node);
        return $this->redirect($this->generateUrl('bluehouseapp_node_index'));
    }



}
