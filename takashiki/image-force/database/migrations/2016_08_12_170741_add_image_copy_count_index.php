<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageCopyCountIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('image', function (Blueprint $table) {
            $table->index(['created_at', 'copy_count']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image', function (Blueprint $table) {
            $table->dropIndex('image_created_at_copy_count_index');
        });
    }
}
