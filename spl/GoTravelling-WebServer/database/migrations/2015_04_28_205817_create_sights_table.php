<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSightsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function(Blueprint $table){
            $tableName = $this->tableName;

            //建立地理坐标的2D索引
            DB::getMongoDB()->$tableName->ensureIndex([
               'loc.coordinates' => '2d'
            ]);

            DB::getMongoDB()->$tableName->ensureIndex([
                'routes' => 1
            ]);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop($this->tableName);
	}

    private $tableName = 'sights';
}
