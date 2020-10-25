<?php

namespace Bluehouseapp\Bundle\WebBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class WelcomeController extends Controller
{

    public function indexAction()
    {
        return $this->render('BluehouseappWebBundle:Frontend/Welcome:index.html.twig',
          array());

    }

    /**
     * @Route("UAIPBaned")
     * @Template("BluehouseappCoreBundle:common:banedIPs.html.twig")

    public function banedIPsAction()
    {
        return  array();
    }    

    

    public function index1Action($name)
    {


        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BluehouseappCoreBundle:Post')->find(2);



        $data = array(
            'some' => 'data' ,
            'goes' => 'here'
        );
        $jsonp = new JsonResponse($data);
       // $jsonp->setCallback('hi');
        $jsonp->headers->set('Content-Type', 'application/json');
        return $jsonp;
    }



     */
}
