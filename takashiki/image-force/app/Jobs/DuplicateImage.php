<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DuplicateImage extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $image;

    /**
     * Create a new job instance.
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        \DB::reconnect();
        $this->image = $image;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->image->duplicate();
    }
}
