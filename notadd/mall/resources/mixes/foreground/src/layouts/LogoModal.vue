<script>
   export default {
       data() {
           return {
               hotSearch: [
                   {
                       name: '买三免一',
                   },
                   {
                       name: '促销',
                   },
                   {
                       name: '五折起购',
                   },
                   {
                       name: '满200减30',
                   },
               ],
           };
       },
   };
</script>
<template>
    <div class="logoModal">
        <div class="container">
            <div class="logo col-md-4 col-xs-4 col-sm-4">
                <router-link to="/mall">
                    <img src="../assets/images/logo.png"/>
                </router-link>
            </div>
            <div class="search-input col-md-6 col-xs-6 col-sm-6">
                <div class="searchInput clearfix">
                    <input type="text" class="form-control" placeholder="洗衣机、电冰箱、洗碗机" aria-describedby="basic-addon2">
                    <span class="input-group-addon" id="basic-addon2">
                        <router-link :to="{name:'search'}">搜索</router-link>
                    </span>
                </div>
                <p>
                    <router-link to="/mall/search"
                                 :key="index"
                                 target="_blank"
                                 v-for="(item,index) in hotSearch">
                        {{ item.name }}
                    </router-link>
                </p>
            </div>
            <div class="shopping-car col-md-2 col-xs-2 col-xs-2">
                <router-link to="/mall/cart-settlement" target="_blank">
                    <i class="icon iconfont icon-gouwuche"></i>
                    购物车
                </router-link>
            </div>
        </div>
    </div>
</template>

