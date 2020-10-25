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

        $this->call(UserTableSeeder::class);
        $this->call(TaskBankSeeder::class);
        $this->call(QuestionBankTableSeeder::class);
        $this->call(TaskQuestionMapTableSeeder::class);
        $this->call(TaskReportSeeder::class);

        Model::reguard();
    }
}
