<?php


namespace App\Command\Task;


use App\Library\Helper\GetterHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    protected static $defaultName = 'task:deploy';

    protected function configure()
    {
        $this->setDescription('清空缓存并修正缓存文件夹权限');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();

        // 清空缓存
        $section_1 = $output->section();
        $progressBar_1 = new ProgressBar($section_1, 1);
        $progressBar_1->setFormat(PHP_EOL . '任务：%message% ' . PHP_EOL . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%');
        $progressBar_1->setMessage('清空缓存');
        $progressBar_1->start();
        $command = $application->find('cache:clear');
        $arguments = [
            'command' => 'cache:clear'
        ];
        $greetInput = new ArrayInput($arguments);
        $nullOutput = new NullOutput();
        $returnCode = $command->run($greetInput, $nullOutput);
        if ($returnCode === 0) {
            $progressBar_1->advance();
        } else {
            $output->writeln('更新缓存失败，错误码：' . $returnCode);
        }
        $progressBar_1->finish();

        // 修正缓存文件夹权限
        $section_2 = $output->section();
        $progressBar_2 = new ProgressBar($section_2, 1);
        $progressBar_2->setFormat(PHP_EOL . '任务：%message% ' . PHP_EOL . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%');
        $progressBar_2->setMessage('修正缓存文件夹权限');
        $progressBar_2->start();
        $cache_dir = GetterHelper::getParameter('kernel.cache_path');
        exec('chmod -R 777 ' . $cache_dir);
        $progressBar_2->advance();
        $progressBar_2->finish();
        $output->writeln('');
    }
}