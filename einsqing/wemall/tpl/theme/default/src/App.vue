<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
	import fetch from 'fetch';
	
  export default {
    name: 'app',
    components: {

    },
    localStorage: {
            cartData: {
                type: Object,
                default: []
            },
            product: {
                type: Object,
                default: []
            },
            menu: {
                type: Object,
                default: []
            },
            user: {
                type: Object,
                default: {}
            },
            token: {
                type: String,
                default: ''
            },
            fee:{
            		type: Object,
                default: []
            },
            defaultAdd:{
            		type: Object,
                default: []
            },
            config:{
            		type: Object,
                default: {}
            }
       },
       created(){
       
       	//获取配置
				fetch("/api/config").then((res)=>{
					if(res){
						this.$localStorage.set('config',res.data.data.config);
						document.title= res.data.data.config.name;
					}
					
				});
       }
  }
</script>

<style>
  @import './assets/css/style.css';
  @import './assets/css/font-awesome.min.css';

  body {
    -webkit-font-smoothing: antialiased;
  }
</style>
