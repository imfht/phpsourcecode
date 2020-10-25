<?php
/**
 * This file is part of Notadd.
 *
 * @datetime 2017-06-24 16:40:59
 */
use Illuminate\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;

/**
 * Class CreateMallUsersTable.
 */
class CreateMallUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('mall_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户 ID');
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
        $this->schema->drop('mall_users');
    }
}
