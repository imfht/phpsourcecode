<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeixinpayNotifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weixinpay_notifies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appid')->default('')->comment('微信开放平台审核通过的应用APPID');
            $table->string('mch_id')->default('')->comment('微信支付分配的商户号');
            $table->string('device_info')->default('')->comment('微信支付分配的终端设备号，');
            $table->string('result_code')->default('')->comment('SUCCESS/FAIL');
            $table->string('err_code')->default('')->comment('错误返回的信息描述');
            $table->string('err_code_des')->default('')->comment('错误返回的信息描述');
            $table->string('openid')->default('')->comment('用户在商户appid下的唯一标识');
            $table->string('trade_type')->default('')->comment('APP');
            $table->string('bank_type')->default('')->comment('银行类型，采用字符串类型的银行标识');
            $table->integer('total_fee')->default(0)->comment('订单总金额，单位为分');
            $table->string('transaction_id')->default('')->comment('微信支付订单号');
            $table->string('out_trade_no')->default('')->comment('商户系统内部订单号');
            $table->string('attach')->default('')->comment('商家数据包，原样返回');
            $table->timestamp('time_end')->nullable()->comment('支付完成时间');

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
        Schema::dropIfExists('weixinpay_notifies');
    }
}
