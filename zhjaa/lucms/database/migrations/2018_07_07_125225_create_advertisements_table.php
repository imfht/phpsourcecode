<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name')->default('')->comment('广告标题');
            $table->integer('cover_image')->default(0)->comment('广告封面图片');
            $table->text('content')->comment('广告内容:json_encode([raw:xxx,html:xxx])');
            $table->text('descriptions')->nullable()->comment('描述');
            $table->integer('weight')->default(20)->comment('权重');
            $table->integer('advertisement_positions_id')->default(0)->comment('所属广告位');
            $table->string('link_url')->default('')->comment('跳转 url:为空则不跳转');
            $table->string('model_column_value')->default('')->comment('json_encode([model=>article,column=>slug,value=markdown-language)');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->enum('enable', ['T', 'F'])->default('F')->comment('启用状态：F禁用，T启用');
            $table->timestamps();
            $table->index('cover_image');
            $table->index('advertisement_positions_id');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertisements');
    }
}
