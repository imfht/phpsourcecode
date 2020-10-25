<?php

namespace App\Console\Commands;

use App\Model\Jobs;
use Illuminate\Console\Command;

class CheckQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检测队列状态是否正常';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Jobs::count('*') > 100) {
        }
    }

}
