<template>
	<div class="login">
		<div class="frame">
			<div class="mdui-card">
				<div class="mdui-card-header mdui-color-theme">
					<img class="mdui-card-header-avatar" src="../../images/doller-tag.png"/>
					<div class="mdui-card-header-title" v-text="PROJECT_NAME"></div>
					<div class="mdui-card-header-subtitle">系统管理后台</div>
				</div>
				<div class="mdui-card-content">
					<div class="content">
						<form method="post" class="form" @keydown.enter="submit">
							<div class="mdui-textfield mdui-textfield-floating-label">
								<i class="mdui-icon material-icons">account_circle</i>
								<label class="mdui-textfield-label">登录名</label>
								<input class="mdui-textfield-input" type="text" id="name" autocomplete="off" v-model="form.name"/>
							</div>
							<div class="mdui-textfield mdui-textfield-floating-label">
								<i class="mdui-icon material-icons">lock</i>
								<label class="mdui-textfield-label">密　码</label>
								<input class="mdui-textfield-input" type="password" v-model="form.password"/>
							</div>
							<div class="mdui-textfield">
								<a class="mdui-btn mdui-ripple mdui-color-theme submit" @click="submit">登录</a>
							</div>
						</form>
					</div>
				</div>
				<div class="mdui-card-actions mdui-text-center login_footer">
					&copy; 2016 {{PROJECT_NAME}} {{domain}} All rights reserved
				</div>
			</div>
		</div>
	</div>
</template>
<script>
    export default {
        data() {
            return {
                PROJECT_NAME: PROJECT_NAME,
                form: {
                    name: '',
                    password: '',
                    captcha: '',
                },
                domain: location.href.substring(7).split('/')[0],
            };
        },
        methods: {
            submit() {
                let t = this;
                t.$API.post('/login', this.form).then(function (data, message) {
                    t.tips(message);
                    t.$emit('init');
                    t.$router.push({name: 'welcome'});
                }).catch(function (msg) {
                    
                });
            }
        },
        mounted() {
            this.$emit('initClear');
            document.getElementById('name').focus();
        }
    };
</script>

<style>
	.frame {
		background-color: #FFF;
		padding: 20px;
		max-width: 36rem;
		margin: 5% auto;
	}
	
	.frame .submit {
		margin-left: 3.5rem;
		cursor: pointer;
	}
	
	.frame .login_footer {
		padding: 1rem 0;
	}
</style>
