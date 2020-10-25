<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelHasTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_has_tags', function (Blueprint $table) {
            $table->unsignedInteger('tag_id');
            $table->morphs('model');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');

            $table->primary(['tag_id', 'model_type', 'model_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_tags');
    }
}
