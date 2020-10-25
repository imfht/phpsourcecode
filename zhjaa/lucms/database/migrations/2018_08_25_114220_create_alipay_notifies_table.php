<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayNotifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay_notifies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type')->default('undefined')->comment('订单类型:一般写表名称:users,取return_param下的第一个值');
            $table->timestamp('notify_time')->nullable()->comment('交易创建时间');
            $table->string('notify_type')->default('')->comment('通知类型');
            $table->string('notify_id')->default('')->comment('通知id');
            $table->string('app_id')->default('')->comment('支付宝分配给开发者的应用Id');
            $table->string('transaction_id')->default('')->comment('支付宝交易凭证号');
            $table->string('order_no')->default('')->comment('服务器订单号');
            $table->string('out_biz_no')->default('')->comment('商户业务号');
            $table->string('trade_state')->default('')->comment('交易状态');
            $table->decimal('amount',9,2)->default(0)->comment('本次交易支付的订单金额，单位为人民币（元）');
            $table->string('subject')->default('')->comment('订单标题');
            $table->string('body')->default('')->comment('商品描述');
            $table->decimal('refund_fee',9,2)->default(0)->comment('退款通知中，返回总退款金额，单位为元，支持两位小数');
            $table->timestamp('trade_create_time')->nullable()->comment('交易创建时间');
            $table->timestamp('pay_time')->nullable()->comment('交易付款时间');
            $table->timestamp('trade_close_time')->nullable()->comment('交易结束时间');
            $table->string('channel')->default('')->comment('支付渠道');
            $table->string('return_param')->default('')->comment('参数：多个以_param_分隔');
            $table->text('other')->comment('json 信息');
            $table->timestamps();

            $table->index('order_no');
            $table->index('transaction_id');
            $table->index('amount');
            $table->index('trade_state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alipay_notifies');
    }
}
