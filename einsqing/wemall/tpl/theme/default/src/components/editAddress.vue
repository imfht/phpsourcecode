<template>
	<div id="app_content" class="snap-content user_addr_admin_bottom" style="padding-bottom: 50px; height: auto; ">
		<div id="user_addr_admin_2" style="background-color: rgb(247, 247, 247); min-height: 572px;">
			<div class="noscroll" style="margin-bottom:60px;">
				<div class="user-shopping-cart user-address-box" style="padding:0;">
					<h3>
            <a href="#/cart" onclick=""></a>收货地址
          </h3>
				</div>
				<div class="add_my_address_wrap needsclick">
					<table class="add_my_address">
						<tbody>
							<tr>
								<td>
									<label for="user_name">收货 <br>姓名</label>
								</td>
								<td>
									<label for="user_name">
                  						<input id="username" class="ng-scope ng-pristine ng-valid needsclick form-control" placeholder="请输入收货人姓名" type="text" v-model='username' style='color:#444;' maxlength="10">
                					</label>
									<input type="hidden" name="id" id="id" value="0">
								</td>
							</tr>
						</tbody>
					</table>
					<table class="add_my_address">
						<tbody>
							<tr>
								<td>
									<label for="user_tel">手机 <br>号码</label>
								</td>
								<td>
									<label for="user_tel">
                  						<input id="tel" class="ng-scope ng-pristine ng-valid needsclick form-control" placeholder="请输入手机号码" type="text" style="color:#444;" v-model='phone' maxlength="11">
                					</label>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="add_my_address">
						<tbody>
							<tr>
								<td><label>收货 <br>地址</label></td>
								<td class="select-area-box" style="position: relative;">
									<!--<span class="td_left">请选择</span>-->
									<select id="hat_city" style="margin-left: 10px;width: 60px;height: 56px; border-width: 0px;background-color: white;" name="hat_city" class="hat_select" v-model="province">
										<option :value="index" v-for='(item,index) in location'>{{item.name}}</option>
									</select>
									<select style="margin-left: 10px;width: 60px;height: 56px; border-width: 0px;background-color: white;" name="hat_area" class="hat_select" v-model='city'>
										<option :value="index" v-for='(item,index) in location[province].sub'>{{item.name}}</option>
									</select>
									<select style="margin-left: 10px;width: 60px;height: 56px; border-width: 0px;background-color: white;" name="hat_area" class="hat_select" v-model='district' v-if='location[province].sub[city]'>

											<option :value="index" v-for='(item,index) in location[province].sub[city].sub'>{{item.name}}</option>

									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="add_my_address">
						<tbody>
							<tr>
								<td>
									<label for="address">具体 <br>地址</label>
								</td>
								<td>
									<label for="address">
                  <input id="address" class="ng-scope ng-pristine ng-valid needsclick form-control" placeholder="请输入具体地址" type="text" v-model='detailAddress' style='color:#444;'>
                </label>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="add_my_address">
						<tbody>
							<tr>
								<td>
									<label for="is_default">设为 <br>默认</label>
								</td>
								<td>
									<label class="user-default-address-wrap" for="is_default">
										<label class="round address-new-add" for="is_default" :class="{ current: isActive}" @click='defaultAdd'></label>
									<input name="is_default" id="is_default" value="0" type="checkbox">
									</label>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div id="app-footer">
				<div class="app-footer-inner">
					<div id="app-footer-main" class="app-header-main">
						<div id="footer" class="footer fixed-wrap">
							<div class="common-footer bd-style">
								<a class="style-color tc" href="javascript:void(0);" title="保存" @click='save'>保存</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="snap-drawers">
				<div class="snap-drawer snap-drawer-right" id="userBase"></div>
			</div>
		</div>
	</div>
</template>

<script>
	import fetch from './../fetch';
	import { Toast } from 'mint-ui';

	export default {
		data() {
			return {
				address: '',
				username: '', //用户名
				phone: '', //手机号
				detailAddress: '', //详细地址
				isActive: false,
				location: [
					{ sub: [] }
				],
				province: 0,
				city: 0,
				district:0,
				districtId:''
			}
		},
		created() {
			//获取省市
			fetch('/api/location').then((res) => {
				if(res.data.code == 1) {
					this.location = res.data.data.location[0].sub;	
					for (let key in this.location) {
                        if (this.location[key].id == this.$route.query.person.province_id) {
                            this.province = key;

                            for(let key1 in this.location[key].sub){
                                if(this.location[key].sub[key1].id == this.$route.query.person.city_id){
                                    this.city = key1;
                                    
                                    for(let key2 in this.location[key].sub[key1].sub){
		                                if(this.location[key].sub[key1].sub[key2].id == this.$route.query.person.district_id){
		                                    this.district = key2;
		                                }
		                            }
                                }
                            }
                        }
                    }
					
				}
			})
			//编辑
			this.username = this.$route.query.person.name;
			this.phone = this.$route.query.person.phone;
			this.detailAddress = this.$route.query.person.address;
		},
		methods: {
			save() {
				if(!this.username) {
					Toast("请输入你的名字");
					return;
				}
				if(!(/^1[358]\d{9}$/.test(this.phone))) {
					Toast('请正确输入手机号');
					return;
				}
				if(!this.detailAddress) {
					Toast("请输入你的详细地址");
					return;
				}
				if(this.location[this.province].sub[this.city].sub[this.district]){
					this.districtId=this.location[this.province].sub[this.city].sub[this.district].id;
				}else{
					this.districtId='';
				}

				//增加用户信息
				fetch('/api/contact', {
					id: this.$route.query.person.id,
					name: this.username,
					phone: this.phone,
					province_id: this.location[this.province].id,
					city_id: this.location[this.province].sub[this.city].id,
					district_id:this.districtId,
					address: this.detailAddress
				}).then((res) => {
					if(res.data.code == 1) { //成功更改
						//通知address发生改变
						this.$bus.emit('addrAdd', res.data.data.contact);
						if(this.isActive) {
							this.$localStorage.set('defaultAdd', res.data.data.contact);
						}
						this.$router.push('/address');
					}
				});
			},
			defaultAdd() {
				this.isActive = !this.isActive;
			}
		}
	}
</script>

<style>
</style>