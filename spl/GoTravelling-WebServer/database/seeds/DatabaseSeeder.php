<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		// $this->call('UserTableSeeder');
        if( Config::get('app.debug') && !App::environment('testing') ){
            $this->call('UserTableTestSeeder');
            $this->call('CollectionTableTestSeeder');
            $this->call('SightTableTestSeeder');
            $this->call('RoutesTableTestSeeder');
            $this->call('CollectionRouteTableTestSeeder');
            $this->call('RouteNotesTableTestSeeder');
        }
	}

}
