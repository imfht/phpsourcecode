<?php

namespace Bluehouseapp\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use  Bluehouseapp\Bundle\CoreBundle\Entity\BanedIPs;
use  Bluehouseapp\Bundle\CoreBundle\Form\BanedIPsType;
use  Bluehouseapp\Bundle\CoreBundle\Controller\Resource\ResourceController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * BanedIPs controller.
 *
 */
class BanedIPsController extends ResourceController
{


}
