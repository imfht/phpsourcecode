<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-29 15:11:08
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallUserIntegralLogsTable.
 */
class CreateMallUserIntegralLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_user_integral_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户 ID');
            $table->integer('integral')->comment('积分值');
            $table->string('comment')->nullable()->comment('注释');
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
        $this->schema->drop('mall_user_integral_logs');
    }
}
