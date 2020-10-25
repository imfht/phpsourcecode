<?php

namespace app\pay\command;

use \think\console\Input;
use \think\console\Output;
use \think\console\Command;

use \think\Db;

class Task extends Command
{

	protected function configure()
	{

		$this->setName('Task')->setDescription('Task');

	}

    protected function execute(Input $input, Output $output)
	{

		$output->writeln('Crontab job start...');
		$this->index();
		$output->writeln('Crontab job finish...');

	}

	private function index()
	{

		if(function_exists('set_time_limit')) {
			set_time_limit(0);
		}

		///
		///

	}

}

