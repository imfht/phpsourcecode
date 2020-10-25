<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdspaces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adspaces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('位置名称');
            $table->integer('is_open')->comment('是否启用');
            $table->string('hash')->comment('hash');

            $table->softDeletes();
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
        Schema::dropIfExists('adspaces');
    }
}
