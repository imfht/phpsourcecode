@extends('layouts.admin')
@section('content')
<!-- Main content -->
<section class="content">
  <!-- row -->
  <div class="row">
    <div class="col-xs-12">
      <div class="box" id="app-content">
        <div class="box-header">
          <h3 class="box-title">
            <button @click="back_action()" type="button" class="btn btn-default pull-left " style="margin:0 0 0 10px;">
             {{trans('admin.website_action_goback')}} 【{{trans('admin.define_model_wechat_mp')}}： {{$website['info']['name']}}】
            </button>
          </h3>
          @include('admin.include.search')
        </div>
        <!-- /.box-header -->
        <div class="box-body" >
          <table class="table table-bordered">
            <thead>
            <tr>
              <th>{{trans('admin.fieldname_item_id')}}</th>
              <th>{{trans('admin.fieldname_item_headimage')}}</th>
              <th>{{trans('admin.fieldname_item_nick')}}</th>
              <th>{{trans('admin.fieldname_item_sex')}}</th>
              <th>{{trans('admin.fieldname_item_province')}}</th>
              <th>{{trans('admin.fieldname_item_city')}}</th>
              <th>{{trans('admin.fieldname_item_country')}}</th>
              <th>{{trans('admin.fieldname_item_score')}}</th>
              <th>{{trans('admin.fieldname_item_money')}}</th>
              <th>{{trans('admin.fieldname_item_subscribe')}}</th>
              <th>{{trans('admin.fieldname_item_status')}}</th>
              <th>{{trans('admin.fieldname_item_option')}}</th>
            </tr>
            </thead>
            <tbody>
              @include('admin.include.fieldvalue.v-for')
                <td>@{{ item.id }}</td>
                <td>
                <image-dialog v-if="item.headimgurl" :style="{width:'60px'}" :thumb="'/uploads/{{getCurrentControllerName()}}/thumb/'+item['headimgurl']"
									:full="'/uploads/{{getCurrentControllerName()}}/thumb/'+item['headimgurl']" ></image-dialog> <i v-else class="fa fa-file-o" ></i></td>
                <td>@{{ item.nickname }}</td>
                <td>@{{ item.sex }}</td>
                <td>@{{ item.province }}</td>
                <td>@{{ item.city }}</td>
                <td>@{{ item.country }}</td>
                <td>@{{ item.score }}</td>
                <td>@{{ item.money }}</td>
                <td><i v-if="item.subscribe == 0"  class="fa fa-toggle-off"> {{trans('admin.website_status_no_subscribe')}} </i> <i v-if="item.subscribe == 1"  class="fa fa-toggle-on"> {{trans('admin.website_status_subscribe')}} </i></td>
                @include('admin.include.fieldvalue.status')
                <td>
                 <div class="tools">
                    @ability('admin', 'set_status')
                    <button v-if="item.status == 1"  type="button" @click="get_one_action(item.id,'status')"  class="btn btn-primary" > <i class="fa fa-toggle-off"></i> {{trans('admin.website_action_status')}}</button>
                    <button v-else  type="button" @click="get_one_action(item.id,'status')"  class="btn btn-danger" > <i class="fa fa-toggle-on"></i> {{trans('admin.website_action_status')}}</button>
                    @endability
                  </div> 
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
        @include('admin.include.pages')
        <!-- /.page -->
      </div>
      <!-- /.box -->
    </div>
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
<template id="image-dialog">
	<div class="image-dialog">
		<button class="image-dialog-trigger" type="button" @click="showDialog"><img :style="style"
				class="image-dialog-thumb"
				ref="thumb" :src="thumb" /></button>
		<div name="dialog" @enter="enter" @leave="leave">
			<div class="image-dialog-background" v-show="appearedDialog" ref="dialog">
				<button class="image-dialog-close" type="button" @click="hideDialog" aria-label="Close"></button><img class="image-dialog-full" ref="full"
					:src="appearedDialog && full"  @load="onLoadFull" />
			</div>
		</div>
	</div>
</template>
<style>
	.image-dialog-trigger {
	margin: 0;
	padding: 0;
	background: none;
	border: none;
	cursor: pointer;
	}
	.image-dialog-close {
	position: absolute;
	right: 20px;
	top: 54px;
	width: 60px;
	height: 60px;
	padding: 0;
	background: none;
	border: none;
	cursor: pointer;
	-webkit-transition: 300ms ease-out;
	transition: 300ms ease-out;
	outline: none;
	}
	.image-dialog-close::before, .image-dialog-close::after {
	content: '';
	position: absolute;
	left: 50%;
	top: 50%;
	margin-top: -0.5px;
	margin-left: -20px;
	width: 40px;
	height: 1px;
	background-color: #000;
	}
	.image-dialog-close::before {
	-webkit-transform: rotate(45deg);
	transform: rotate(45deg);
	}
	.image-dialog-close::after {
	-webkit-transform: rotate(135deg);
	transform: rotate(135deg);
	}
	.image-dialog-close:hover {
	-webkit-transform: rotate(270deg);
	transform: rotate(270deg);
	}
	.image-dialog-background {
	overflow: auto;
	position: fixed;
	top: 0;
	right: 0;
	left: 0;
	bottom: 0;
	padding: 80px 80px;
	background-color: rgba(255, 255, 255, 0.9);
  text-align: center;
  z-index:4;
	}
	.image-dialog-animate {
	display: none;
	position: absolute;
	-webkit-transform-origin: left top;
	transform-origin: left top;
	}
	.image-dialog-animate.loading {
	display: block;
	}

	.dialog-enter-active, .dialog-leave-active {
	-webkit-transition: background-color 300ms ease-out;
	transition: background-color 300ms ease-out;
	}
	.dialog-enter, .dialog-leave-to {
	background-color: rgba(255, 255, 255, 0);
	}
	.dialog-enter-active .image-dialog-animate, .dialog-leave-active .image-dialog-animate {
	display: block;
	-webkit-transition: -webkit-transform 300ms cubic-bezier(1, 0, 0.7, 1);
	transition: -webkit-transform 300ms cubic-bezier(1, 0, 0.7, 1);
	transition: transform 300ms cubic-bezier(1, 0, 0.7, 1);
	transition: transform 300ms cubic-bezier(1, 0, 0.7, 1), -webkit-transform 300ms cubic-bezier(1, 0, 0.7, 1);
	}
	.dialog-enter-active .image-dialog-full, .dialog-leave-active .image-dialog-full {
	visibility: hidden;
	}
	.image-dialog-thumb{
		max-height: 60px;
	}
	.image-dialog-full{
		margin: 40px auto 0;
		display: block;
		max-width:400px;
		max-height: 400px;
	}
</style>
<script type="text/javascript">
Vue.component('image-dialog', { 
	template: '#image-dialog', 
  props: { 
    thumb: String, 
    full: String, 
    style:Object,
  }, 
  data () { 
    return { 
    loaded: false, 
    appearedDialog: false 
    } 
  }, 
  methods: { 
    showDialog () { 
    this.appearedDialog = true 
    }, 
    
    hideDialog () { 
    this.appearedDialog = false 
    }, 
    
    enter () { 
    this.animateImage( 
      this.$refs.thumb, 
      this.$refs.full 
    ) 
    }, 
    
    leave () { 
    this.animateImage( 
      this.$refs.full, 
      this.$refs.thumb 
    ) 
    }, 
    
    onLoadFull () { 
    this.loaded = true 
    }, 
  
    animateImage (startEl, destEl) { 
    const start = this.getBoundForDialog(startEl) 
    this.setStart(start) 
    
    this.$nextTick(() => { 
      const dest = this.getBoundForDialog(destEl) 
      this.setDestination(start, { 
      top: dest.top, 
      left: dest.left, 
      width: dest.width,
      height: dest.height
      }) 
    }) 
    }, 
    
    getBoundForDialog (el) { 
    const bound = el.getBoundingClientRect() 
    const dialog = this.$refs.dialog 
    return { 
      top: bound.top + dialog.scrollTop, 
      left: bound.left + dialog.scrollLeft, 
      width: bound.width, 
      height: bound.height 
    } 
    }, 
    
    setStart (start) { 
    const el = this.$refs.animate 
    el.style.left = start.left + 'px' 
    el.style.top = start.top + 'px' 
    el.style.width = start.width + 'px' 
    el.style.height = start.height + 'px' 
    el.style.transitionDuration = '0s' 
    el.style.transform = '' 
    }, 
    
    setDestination (start, dest) { 
    const el = this.$refs.animate 
    el.style.transitionDuration = '' 
    
    const translate = `translate(${dest.left - start.left}px, ${dest.top - start.top}px)` 
    const scale = `scale(${dest.width / start.width}, ${dest.height / start.height})` 
    el.style.transform = `${translate} ${scale}` 
    } 
  } 
});
new Vue({
    @include('admin.include.vue-el')
    data: {
             apiurl_list          :'{{ route("post.admin.wechatuser.api_list") }}',
             linkurl_back         :'{{ route("get.admin.wechat.manage") }}/{{$website["wechat_id"]}}',
             @include('admin.include.vue-apiurl-action-one')
             @include('admin.include.vue-apiurl-action-delete')
             @include('admin.include.vue-pages-dataitems')
             @include('admin.include.vue-pages-pageparams-wechat')
             @include('admin.include.vue-pages-paramsdata')
          },
    @include('admin.include.vue-ready')
    @include('admin.include.vue-pages-computed')
    methods: {
            @include('admin.include.vue-methods-action_list_get')
            @include('admin.include.vue-methods-action_list_do')
            @include('admin.include.vue-methods-action_list_search')
            @include('admin.include.vue-methods-action_one_get')
            @include('admin.include.vue-methods-action_info_return')
            @include('admin.include.vue-methods-link_click_page')
            @include('admin.include.vue-methods-link_click_back')
            @include('admin.include.vue-methods-link_click_delete')
        }            
})

</script>
@endsection