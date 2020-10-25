<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisement_positions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name')->default('')->comment('广告位名称');
            $table->string('description')->default('')->comment('广告位描述');
            $table->enum('type', ['default', 'model', 'spa'])->default('default')->comment('广告位类型:默认、跳转到模型、单页面');
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
        Schema::dropIfExists('advertisement_positions');
    }
}
