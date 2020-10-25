<template>
  <div id="app_content" class="snap-content">
    <div class="complete-box" id="complete-box" style="min-height: 500px; ">
      <div class="complete-wrap">
        <table>
          <tbody>
          <tr>
            <td>
              <img src="../assets/img/complete-status.png" width="35" height="35">
            </td>
            <td>
              <p style="font-size: 1.3em;">亲,你已成功下单</p>
              <p>订单号:<span id="result-order-no">{{order.orderid}}</span></p>
            </td>
          </tr>
          </tbody>
        </table>
        <div class="complete-fruit-box">
          <img src="../assets/img/complete-fruit.png?v=1" width="80%">
          <p>送货员正全力奔向你啦～</p></div>
        <div class="complete-inner complete-inner-cancel">
          <p class="cont-tip">您的订单我们将在约定的时间送达，谢谢！收货时间在{{order.delivery_time}}时间段内，请留意您的手机。</p>
        </div>
        <p class="tc">
          <input type="hidden" id="event-mark" value="">
          <input type="hidden" id="event-type" value="share_count"> <input type="hidden" id="event-model" value="">
        </p>
      </div>
    </div>
    <div class="navbar navbar-app navbar-absolute-bottom">
      <div class="complete-footer">
        <div class="complete-footer-inner">
          <a href="#/home" class="back2homeBtn border-radius ft-color">我知道了</a>
          <a href="#/myorder" class="viewMyOrderBtn border-radius">查看我的订单</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
	import fetch from './../fetch';
    export default{
        data(){
            return {
                order: {}
            }
        },
        created() {
            fetch(`/api/order/${this.$route.query.id}`).then((res) => {
                this.order = res.data.data.order;
            });
        }
    }
</script>

<style>
</style>
