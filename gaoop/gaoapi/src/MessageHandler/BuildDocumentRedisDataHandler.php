<?php


namespace App\MessageHandler;


use App\Message\BuildDocumentRedisData;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BuildDocumentRedisDataHandler implements MessageHandlerInterface
{
    private $kernel;

    private $info_id;

    private $show;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function __invoke(BuildDocumentRedisData $data)
    {
        $this->info_id = $data->getInfoId();
        $this->show = $data->getShow();

        $this->do();
    }

    public function do()
    {
        $application = new Application($this->kernel);
        $command = $application->find('build:document-redis-data');
        $arguments = [
            'command' => 'build:document-redis-data',
            'info_id' => $this->info_id,
            '--show' => $this->show
        ];
        $greetInput = new ArrayInput($arguments);
        $output = new NullOutput();
        $returnCode = $command->run($greetInput, $output);
    }
}