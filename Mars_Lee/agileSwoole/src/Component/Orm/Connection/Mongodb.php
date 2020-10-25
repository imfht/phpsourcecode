<?php


namespace Component\Orm\Connection;

use Kernel\Core\Conf\Config;
use Kernel\Core\IComponent\IConnection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;


class Mongodb implements IConnection
{
    use HashCode;
    use Free;
        protected $manager;
        public function __construct(Config $config)
        {
                $config = $config->get('mongodb');
                try {
                        $manager = new Manager($config['uri'], $config['uriOptions']??[]);
                        $command = new Command(['ping' => 1]);
                        $manager->executeCommand('db', $command);

                } catch (\exception $e) {
                        throw new \InvalidArgumentException('Connection failed: '.$e->getMessage(), $e->getCode());
                }

                $this->manager = $manager;
            $this->HashCode();
        }

        public function getManager()
        {
                return $this->manager;
        }


}