<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplyRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm_apply_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('tree_trunk_id');
            $table->integer('user_id');
            $table->string('name',20);
            $table->smallInteger('main_type');
            $table->string('type');
            $table->string('number');// 如果是申请购买新设备，则不需要此字段；如果是申请使用，则使用此字段。
            //可以有多个审批人，使用 ‘#’ 将多个审批人分割开来。 可以使用一个工具类来操作 approvers 和对应的 statuses
            $table->string('approvers');
            //可以有多个审批人，使用 ‘#’ 将多个审批人审批的状态分割开来；可以用 0表示未审批，1表示同意，2表示否认
            $table->string('statuses');
            $table->double('price');
            $table->unsignedMediumInteger('quantity');// 如果是申请使用，则不需要此字段；如果是申请购买，则需要
            $table->text('description');
            $table->timestamps();
            $table->tinyInteger('delete')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_apply_records');
    }
}
