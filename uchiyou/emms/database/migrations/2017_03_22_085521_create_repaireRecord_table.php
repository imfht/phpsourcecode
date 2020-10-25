<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepaireRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('mm_repaire_record', function (Blueprint $table) {
    		$table->increments('id');
    		$table->integer('user_id');
    		$table->integer('company_id');
    		$table->integer('material_id');
    		$table->text('fault_description');
    		$table->tinyInteger('status');
    		$table->tinyInteger('delete')->default(1);
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
