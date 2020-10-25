<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('port',['A'])->default('A')->comment('留置有多个 app 的情况');
            $table->enum('system',['ANDROID','IOS','ALL'])->default('ANDROID');
            $table->string('version_sn')->default('1.0.0');
            $table->string('version_intro')->default('');
            $table->integer('package')->default(0)->comment('对应的包');
            $table->timestamps();

            $table->index('system');
            $table->index('version_sn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_versions');
    }
}
