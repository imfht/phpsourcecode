<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_records', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); //制造商名称
            $table->string('description')->nullable();  //描述
            $table->string('location')->nullable(); //所在国家、地区
            $table->string('creator');
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
        Schema::dropIfExists('vendor_records');
    }
}
