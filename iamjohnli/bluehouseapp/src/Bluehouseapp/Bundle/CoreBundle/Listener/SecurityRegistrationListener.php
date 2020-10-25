<?php

namespace Bluehouseapp\Bundle\CoreBundle\Listener;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Bluehouseapp\Bundle\CoreBundle\Entity\UserBehavior;

/**
 * Listener responsible to log user registration behavior
 * author :michaelwang
 * contact : michael.zhenhua.wang@gmail.com
 */
class SecurityRegistrationListener implements EventSubscriberInterface
{
    protected $em;

    protected $route;
    
    public function __construct($em,$route)
    {
        $this->em = $em;
        $this->route = $route;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
        );
    }

    public function onRegistrationInitialize(UserEvent $event)
    {
       $request =  $event->getRequest();
       $ip = $request->getClientIp(false);
        if($ip!=''){
            $banedIPsRepo = $this->em->getRepository('BluehouseappCoreBundle:BanedIPs');
            $banedIP =  $banedIPsRepo->findByIp($ip);
            if ($banedIP != null){
                $event->setResponse(new RedirectResponse('/UAIPBaned'));
            }
        }

    }
    
    public function onRegistrationSuccess(UserEvent $event)
    {

        $request =  $event->getRequest();
        $ip = $request->getClientIp(false);
        if($ip!=''){
            $userBehaviorRepo = $this->em->getRepository('BluehouseappCoreBundle:UserBehavior');
            $userBehavior = new UserBehavior();
            $userBehavior->setActionName('REGISTRATION_COMPLETED');
            $userBehavior->setUserId($event->getUser()->getId());
            $userBehavior->setClientIP($ip);
            $this->em->persist($userBehavior);
            $this->em->flush();
        }


    }
}