<?php

namespace Bluehouseapp\Bundle\CoreBundle\Listener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Custom login listener.
 * author :michaelwang
 * contact : michael.zhenhua.wang@gmail.com
 */
class LoginListener
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    protected $route;

    protected $dispatcher;
    
    public function __construct($em,$route,$dispatcher)
    {
        $this->em = $em;
        $this->route = $route;
        $this->dispatcher = $dispatcher;
    }
    

    /**
     * Do the magic.
     * 
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
       $request =  $event->getRequest();
       $ip = $request->getClientIp(false);
       $banedIPsRepo = $this->em->getRepository('BluehouseappCoreBundle:BanedIPs');
       $banedIP =  $banedIPsRepo->findByIp($ip);
       if ($banedIP != null){
          $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));  
       }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $event->setResponse(new RedirectResponse('/UAIPBaned'));
    }
}

