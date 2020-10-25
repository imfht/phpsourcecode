<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Trsteel\CkeditorBundle\TrsteelCkeditorBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),
            new Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle(),
            new Knp\Bundle\TimeBundle\KnpTimeBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new Bluehouseapp\Bundle\CoreBundle\BluehouseappCoreBundle(),
            new Bluehouseapp\Bundle\WebBundle\BluehouseappWebBundle(),
            new Bluehouseapp\Bundle\ApiBundle\BluehouseappApiBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();

        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
