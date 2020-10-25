<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create('mm_config', function (Blueprint $table) {
    		$table->increments('id');
    		$table->string('name');
    		$table->string('key');
    		$table->string('value');
    		$table->integer('config_tree_trunk_id');
    		$table->integer('config_company_id');
    		$table->string('description');
    		$table->rememberToken();
    		$table->timestamps();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
