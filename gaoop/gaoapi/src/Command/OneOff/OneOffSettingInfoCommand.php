<?php


namespace App\Command\OneOff;


use App\Library\Helper\GeneralHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OneOffSettingInfoCommand extends Command
{
    protected static $defaultName = 'one-off:setting-info';

    private $container;

    private $em;

    private $open_api_config_path;

    public function __construct(ContainerInterface $container, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->open_api_config_path = $this->container->getParameter('app.swagger.openapi.path');
    }

    protected function configure()
    {
        $this->setDescription('同步未关联的元信息的数据.')
            ->setHelp('同步未关联的元信息的数据');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $info_id = GeneralHelper::getOneInstance()->getCurrentInfoId();

        // 更新parameters
        $parameter_update = $this->em->createQuery('update App\Entity\Parameters p set p.infoId = :infoId')
            ->setParameter('infoId', $info_id)
            ->execute();

        // 更新paths
        $paths_update = $this->em->createQuery('update App\Entity\Paths p set p.infoId = :infoId')
            ->setParameter('infoId', $info_id)
            ->execute();

        // 更新servers
        $servers_update = $this->em->createQuery('update App\Entity\Servers s set s.infoId = :infoId')
            ->setParameter('infoId', $info_id)
            ->execute();

        // 更新tags
        $tags_update = $this->em->createQuery('update App\Entity\Tags t set t.infoId = :infoId')
            ->setParameter('infoId', $info_id)
            ->execute();
    }
}