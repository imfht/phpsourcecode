<style lang="less">
  @import './login.less';
</style>

<template>
  <div class="login">
    <div class="login-con" >
      <Card icon="log-in" title="欢迎登录" :bordered="false">
        <div class="form-con">
          <login-form @on-success-valid="handleSubmit"></login-form>
          <p class="login-tip">请输入用户名和密码</p>
        </div>
      </Card>
    </div>
  </div>
</template>

<script>
import LoginForm from '_c/login-form'
import { mapActions } from 'vuex'
import {Notice} from 'iview'
export default {
  components: {
    LoginForm
  },
  methods: {
    ...mapActions([
      'handleLogin',
      'getUserInfo'
    ]),
    handleSubmit ({ email, password }) {
      this.handleLogin({ email, password }).then(res => {
        this.getUserInfo().then(res => {
          this.$router.push({
            name: 'home'
          })
        })
      }).catch(function (e) {
        Notice.error({title: '系统错误：请修复后重试', desc:e})
      })
    }
  }
}
</script>

<style>

</style>
