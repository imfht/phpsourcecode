<?php

use Illuminate\Database\Seeder;
use App\Model\Page;

class PagesSetValue extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $building = Page::where('url','building')->first();
        if(!$building){
            $building = new Page();
            $building->url = 'building';
            $building->view = 'building';
        }
        $building->is_open = 1;
        $building->save();
    }
}
