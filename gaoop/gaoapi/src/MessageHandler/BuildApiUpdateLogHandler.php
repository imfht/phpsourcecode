<?php


namespace App\MessageHandler;


use App\Message\BuildApiUpdateLog;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BuildApiUpdateLogHandler implements MessageHandlerInterface
{
    private $kernel;

    private $info_id;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function __invoke(BuildApiUpdateLog $buildApiUpdateLog)
    {
        $this->info_id = $buildApiUpdateLog->getInfoId();

        $this->do();
    }

    public function do()
    {
        $application = new Application($this->kernel);
        $command = $application->find('build:api-log');
        $arguments = [
            'command' => 'build:api-log',
            'info_id' => $this->info_id
        ];
        $greetInput = new ArrayInput($arguments);
        $output = new NullOutput();
        $returnCode = $command->run($greetInput, $output);
    }
}