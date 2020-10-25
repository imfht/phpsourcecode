<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\ImageStorage;
use Illuminate\Console\Command;

class CheckImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:image {from?} {to?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检测可用备份数不足的图片并进行备份';

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
        $from = date('Y-m-d H:i:s', strtotime($this->argument('from') ?: 'today'));
        $to = date('Y-m-d H:i:s', strtotime($this->argument('to') ?: 'now'));

        while (
            $images = Image::whereBetween('created_at', [$from, $to])
                ->where('copy_count', '<', ImageStorage::count())
                ->where('copy_count', '>', 0)
                ->take(10)
                ->get()
        ) {
            if ($images->isEmpty()) {
                $this->info('No image to process.');
                break;
            }
            foreach ($images as $image) {
                $image->checkMulti();
                $this->info("Image:{$image->id} processed.");
            }
        }
    }
}
