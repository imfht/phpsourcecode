<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 14-12-23
 * Time: 下午4:41
 */

namespace Bluehouseapp\Bundle\CoreBundle\Listener;
use Bluehouseapp\Bundle\CoreBundle\Entity\Node;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Bluehouseapp\Bundle\CoreBundle\Entity\Member;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
class ModelSetImageURLListener implements EventSubscriber{

    private $helper;
    private $liip_imagine_manager;
    private  $image_data_manager;

    public function __construct(UploaderHelper $helper,CacheManager $liip_imagine_manager )
    {
        $this->helper = $helper;
        $this->liip_imagine_manager = $liip_imagine_manager;


    }

    public function getSubscribedEvents()
    {
        return array(
            'postLoad'
        );
    }
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ( $entity instanceof Member) {

            $accessor = PropertyAccess::createPropertyAccessor();

            $userImage=$accessor->getValue($entity, 'userImage');
            if($userImage!=null){
                $path = $this->helper->asset($entity, 'userImage');
                if($path!=null){
                    $filter="app_mini_image";
                 $filterPath=$this->liip_imagine_manager->getBrowserPath($path,$filter,false);


                    $entity->setUserImageURL($filterPath);
                }

            }else{
                $defaultMemberImageURL="/bundles/bluehouseappweb/images/user_default_mini.png";

                $entity->setUserImageURL($defaultMemberImageURL);
            }

        }

        if ( $entity instanceof Node) {

            $accessor = PropertyAccess::createPropertyAccessor();

            $NodeImage=$accessor->getValue($entity, 'image');
            if($NodeImage!=null){
                $path = $this->helper->asset($entity, 'image');
                if($path!=null){
                    $filter="app_avatar_image";
                    $filterPath=$this->liip_imagine_manager->getBrowserPath($path,$filter,false);
                    $entity->setNodeimageurl($filterPath);
                }

            }

        }
    }

} 