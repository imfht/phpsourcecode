<template>
  <div class="snap-content">
    <div class="app-header box-sizing fixed-wrap" style="position: fixed">
      <div class="app-header-inner">
        <div class="app-header-top">
          <div class="header-focus hide" id="header-focus">
            <div> 你还没关注，关注有新用户专享优惠哦！<a
              href="http://mp.weixin.qq.com/s?__biz=MzA4NzAwMzAwOQ==&amp;mid=200863069&amp;idx=1&amp;sn=9d5d2b8884ab97d90c8ab6532df958db#rd"
              title="去关注">去关注</a></div>
          </div>
        </div>
        <div class="app-header-main">
          <div class="header-menu">
            <table border="0" cellpadding="0" cellspacing="0">
              <tbody>
              <tr>
                <td class="current-part" style="width: 22px; "></td>
                <td >
                  	<ul style="overflow: scroll;width: 312px;white-space: nowrap;">
	                    <li data-class="fruits" style="width:70px;" v-for="item in menu">
	                      <a v-bind:class="[ selectedMenu == item.id? 'current': '' ]" @click="selectMenu(item.id)">
	                        <span>{{item.name}}</span>
	                      </a>
	                    </li>
                  	</ul>
                </td>
                <td class="active-btn" style=''>
                  <router-link to="/user">
                    <img src="../assets/img/account.png" width="25px">
                  </router-link>
                  <i></i>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import fetch from "./../fetch";
  import {getFile} from "./../util";

  export default {
    data() {
      return {
        menu: [],
        selectedMenu: 0
      }
    },
//  watch: {
//    'selectedMenu': function(oldVal, newVal) {
//      this.$emit('selectedMenu', newVal);
//    }
//  },
    created() {

      this.menu = this.$localStorage.get('menu');

      this.initMenu();

      fetch('/api/product/category').then((res) => {
        if (res) {
          this.menu = res.data.data.category;

          // 缓存菜单
          this.$localStorage.set('menu', this.menu);

          // 设置默认第一个菜单
          this.initMenu();
        }
      });
    },
    methods: {
      selectMenu(id) {
        this.selectedMenu = id;

        this.$localStorage.set('selectedMenu', this.selectedMenu);
        
        this.$emit('selectedMenu', id);
      },
      initMenu() {
        if (!this.selectedMenu && this.menu.length) {
          this.selectMenu(this.menu[0].id);
        }
      }
    }
  }
</script>

<style>
</style>
