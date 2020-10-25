<?php


namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class MessengerRestartCommand extends Command
{
    protected static $defaultName = 'messenger:restart';

    protected function configure()
    {
        $this->setDescription('重启messenger队列');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();

        // 停止
        $command = $application->find('messenger:stop-workers');
        $arguments = [
            'command' => 'messenger:stop-workers'
        ];
        $greetInput = new ArrayInput($arguments);
        $output = new NullOutput();
        $returnCode = $command->run($greetInput, $output);

        // 启动
        $command = $application->find('messenger:consume');
        $application->setAutoExit(true);
        $arguments = [
            'command' => 'messenger:consume',
            'receivers' => ['async'],
        ];
        $greetInput = new ArrayInput($arguments);
        $output = new NullOutput();
        $returnCode = $command->run($greetInput, $output);
    }
}