<?php
/**
 * @Author: Wang Chunsheng
 * @Date:   2020-04-29 02:20:18
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 20:48:23
 */
use common\widgets\adminlte\VuemainAsset;
use richardfan\widget\JSRegister;

VuemainAsset::register($this);

?>
<style>
    .topnavmenu{
        border-radius: 4px 4px 0 0;
    }
</style>
<el-menu  active-text-color="#409EFF" :default-active="activeIndex" class="el-menu-demo topnavmenu" mode="horizontal" @select="handleSelect" id="_tab"  v-show="items.length>0"  v-cloak>
    <el-menu-item :index="'menu'+index" v-for="(menu,index) in items" :key="index" :class="menu.active?'is-active':''">
        <a :href="menu.url"  v-if="menu.label">{{menu.label}}</a>
    </el-menu-item>
    <el-menu-item class="pull-right">
        <el-link icon="glyphicon glyphicon-refresh" @click="reloadpage">刷新</el-link>
        <el-link  @click="historypage"> <i class="fa fa-mail-reply" ></i>返回 </el-link>
    </el-menu-item>
</el-menu>

<?php JSRegister::begin([
    'key' => '_tab',
]); ?>
<script>
    new Vue({
        el: '#_tab',
        data: function() {
            return { 
                items:<?= $items; ?>,
                activeIndex:'menu0',
            }
        },
        created: function () {
            let that  = this; 
            that.items.forEach((item,key)=>{
                if(item.active){
                    that.activeIndex = 'menu'+key
                }
            })
        },
        methods:{
            handleSelect(key, keyPath) {
                let that = this
                that.items.forEach((item,k)=>{
                    if('menu'+k == key){
                        window.location.href = item.url
                    }
                })
            },
            reloadpage(){
                window.location.reload()
            },
            historypage(){
                history.go(-1);
            }
        }
    })
</script>
<?php JSRegister::end(); ?>