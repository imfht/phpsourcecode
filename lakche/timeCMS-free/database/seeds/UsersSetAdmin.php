<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersSetAdmin extends Seeder
{
    public function run()
    {
        $admin = User::sortBy('id')->first();
        if(!$admin){
            $admin = new User();
            $admin->name = 'admin';
            $admin->password = Hash::make('123456');
        }
        $admin->is_admin = 1;
        $admin->save();
    }
}
