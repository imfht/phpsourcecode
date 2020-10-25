<template>
	<div id="app_content" class="snap-content user_addr_admin_bottom" style="padding-bottom: 50px; height: auto; ">
		<div id="user_addr_admin_1" style="background-color: rgb(247, 247, 247); min-height: 522px; ">
			<div id="deliveryaddress" class="scrollable">
				<div id="list" class="scrollable-content" ng-show="delivery_act=='list'">
					<div class="user-shopping-cart user-address-box" style="margin:0; padding:0;">
						<h3>
              				<a href="#/cart"></a>收货地址
            			</h3>
						<a href="javascript:void(0);" class="address_admin_btn" id="address_admin_btn" @click='control' v-show='isEdit'>管理</a>
						<a href="javascript:void(0);" class="address_admin_btn" id="address_admin_btn" @click='control' v-show='!isEdit'>完成</a>
					</div>
					<div class="cart-settlement-wrap my-address-manage" style="padding:0;background:none;">
						<div class="my-address-manage-inner" style="padding:0;">
							<div class="my-address-item my-address-opera-wrap" id="my-address-103632" v-for='(person,index) in personList' :class='{"my-address-item-cur":currentId==person.id}' @click='selectAdd(person)'>
								<div class="my-address-opera-inner" style='display: inline-block;' v-show='!isEdit'>
									<ul>
										<li>
											<a href="javascript:void(0);" @click.stop='edit(person)'>编辑</a>
										</li>
										<li>
											<a href="javascript:void(0);" @click.stop='del(person,index)'>删除</a>
										</li>
									</ul>
								</div>
								<table class="cart-settlement-inner cart-settlement-inner-bg user-info">
									<tbody>
										<tr>
											<td class="user-shopping-cart" style="font-size: 1em;color:#777">
												<p>
													<span class="letter_spacing">收货人</span>：{{person.name}}
												</p>
												<p>联系电话：<span id="contact-phone">{{person.phone}}</span></p>
												<p>
													<span style="color: #ffab19" v-show='person.border'>(默认)</span> 详细地址：
													<span id="contact-address" v-if='person.province'>{{person.province.name}}</span>
													<span id="contact-address" v-if='person.city'>{{person.city.name}}  </span>
													<span id="contact-address" v-if='person.district'>{{person.district.name}}</span>
													<span id="contact-address">{{person.address}}</span>
												</p>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="app-footer">
				<div class="app-footer-inner">
					<div id="app-footer-main" class="app-header-main">
						<div id="footer" class="footer fixed-wrap">
							<div class="common-footer bd-style">
								<router-link to="/addAddress" class="style-color tc" title="新增收货地址">
									新增收货地址
								</router-link>
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
	import Vue from 'vue'
	import fetch from './../fetch'
	import { Toast, Indicator } from 'mint-ui';

	export default {
		data() {
			return {
				personList: [],
				province: '',
				city: '',
				district: '',
				isEdit: true,
				defaultAdd: [],
				currentId: 0
			}
		},
		created() {
			//默认地址
			this.defaultAdd = this.$localStorage.get('defaultAdd');
			this.currentId = this.$localStorage.get('currentId')
			//获取收货人列表
			fetch("/api/contact").then((res) => {
				if(res&&res.data.data) {
					this.personList = res.data.data.contact;
					for(let key in this.personList) {
						if(this.personList[key].id == this.defaultAdd.id) {
							Vue.set(this.personList[key], 'border', true)
						}
					}
				}
			})
			//新增地址
			this.$bus.on('addrAdd', this.addrAdd);
		},
		methods: {
			//新增地址
			addrAdd(item) {
				this.personList.push(item);
			},
			control() {
				this.isEdit = !this.isEdit;
			},
			//编辑
			edit(person) {
				this.$router.push({ path: 'editAddress', query: { person: person } })
			},
			//删除
			del(person, index) {
				//删除默认地址
				if(person.border) {
					localStorage.defaultAdd = '';
				}
				fetch('/api/contact', {
					id: person.id,
					status: 0
				}).then((res) => {
					if(res.data.code == 1) { //成功删除
						this.personList.splice(index, 1)
					}
				});
			},
			//选择地址
			selectAdd(person) {
				this.currentId = person.id;
				this.$localStorage.set('currentId', this.currentId)
				this.$router.push({ path: 'cart', query: { address: person } });

			}
		}

	}
</script>

<style>

</style>