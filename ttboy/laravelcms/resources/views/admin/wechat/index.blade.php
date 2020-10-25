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
            @include('admin.include.button-add')
          </h3>
          @include('admin.include.search')
        </div>
        <!-- /.box-header -->
        <div class="box-body" >
          <table class="table table-bordered">
            <thead>
            <tr>
              <th>{{trans('admin.fieldname_item_id')}}</th>
              <th>{{trans('admin.fieldname_item_type')}}</th>
              <th>{{trans('admin.fieldname_item_token')}}</th>
              <th>{{trans('admin.fieldname_item_name')}}</th>
              <th>{{trans('admin.fieldname_item_logo')}}</th>
              <th>{{trans('admin.fieldname_item_gid')}}</th>
              <th>{{trans('admin.fieldname_item_status')}}</th>
              <th>{{trans('admin.fieldname_item_option')}}</th>
            </tr>
            </thead>
            <tbody>
              @include('admin.include.fieldvalue.v-for')
                <td>@{{ item.id }}</td>
                <td v-if="item.type == 1"> <i class="fa fa-wechat"></i> {{trans('admin.define_model_wechat1')}}</td>
                <td v-if="item.type == 2"> <i class="fa fa-wechat"></i> {{trans('admin.define_model_wechat2')}}</td>
                <td v-if="item.type == 3"> <i class="fa fa-wechat"></i> {{trans('admin.define_model_wechat3')}}</td>
                <td v-if="item.type == 4"> <i class="fa fa-wechat"></i> {{trans('admin.define_model_wechat4')}}</td>
                <td v-if="item.type == 5"> <i class="fa fa-wechat"></i> {{trans('admin.define_model_wechat5')}}</td>
                <td>@{{ item.token }}</td>
                <td>@{{ item.name }}</td>
                <td>
                <image-dialog v-if="item.isattach == 1" :style="{width:'60px'}" :thumb="'/uploads/{{getCurrentControllerName()}}/thumb/'+item['attachment']"
									:full="'/uploads/{{getCurrentControllerName()}}/thumb/'+item['attachment']" ></image-dialog><i v-else class="fa fa-file-o" ></i></td>
                <td>@{{ item.gid }}</td>
                <td><i v-if="item.status == 0"  class="fa fa-toggle-off"> {{trans('admin.website_status_off')}} </i> <i v-if="item.status == 1"  class="fa fa-toggle-on"> {{trans('admin.website_status_on')}} </i></td>
                <td>
                  <div class="tools">
                    @ability('admin', 'edit')
                    <button type="button" @click="edit_action(item.id)" class="btn btn-primary" > <i class="fa fa-edit"></i> {{trans('admin.website_action_edit')}}</button>
                    @endability
                    @ability('admin', 'set_status')
                    <button v-if="item.status == 1"  type="button" @click="get_one_action(item.id,'status')"  class="btn btn-primary" > <i class="fa fa-toggle-off"></i> {{trans('admin.website_action_status')}}</button>
                    <button v-else  type="button" @click="get_one_action(item.id,'status')"  class="btn btn-danger" > <i class="fa fa-toggle-on"></i> {{trans('admin.website_action_status')}}</button>
                    @endability
                    <button type="button" @click="link_manage_action(item.id)"  class="btn btn-danger" > <i class="fa fa-certificate"></i> {{trans('admin.website_navigation_management_center')}}</button>
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
             apiurl_list          :'{{ route("post.admin.wechat.api_list") }}',
             linkurl_add          :'{{ route("get.admin.wechat.add") }}',
             linkurl_edit         :'{{ route("get.admin.wechat.edit") }}/',
             linkurl_manage       :'{{ route("get.admin.wechat.manage") }}/',
             @include('admin.include.vue-apiurl-action-one')
             @include('admin.include.vue-pages-dataitems')
             @include('admin.include.vue-pages-pageparams')
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
            @include('admin.include.vue-methods-link_click_add')
            @include('admin.include.vue-methods-link_click_edit')
            @include('admin.include.vue-methods-link_click_page')
            link_manage_action:function(id)
            {
                window.location.href=this.linkurl_manage+id;
            },
        }            
})

</script>
@endsection