<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UsersSetAdmin::class);
        $this->call(SystemsSetValue::class);
        $this->call(CategoriesSetValue::class);
        $this->call(PagesSetValue::class);

        Model::reguard();
    }
}
