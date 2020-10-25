<?php

namespace Bluehouseapp\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use  Bluehouseapp\Bundle\CoreBundle\Entity\Category;
use  Bluehouseapp\Bundle\CoreBundle\Form\CategoryType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Bluehouseapp\Bundle\CoreBundle\Controller\Resource\ResourceController;
/**
 * Category controller.
 *
 */
class CategoryController extends ResourceController
{


    /**
     */
    public function enableAction(Request $request,$id)
    {
        $category =$this->getRepository()
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $category->setEnabled(true);
        $em->flush($category);
        return $this->redirect($this->generateUrl('bluehouseapp_category_index'));
    }

    /**
     */
    public function disableAction(Request $request,$id)
    {
        $category = $this->getRepository()
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $category->setEnabled(false);
        $em->flush($category);
        return $this->redirect($this->generateUrl('bluehouseapp_category_index'));
    }
}
