import { login, logout, getUserInfo } from '@/api/user'
import { setToken, getToken } from '@/libs/util'

export default {
  state: {
    userId: '',
    email: '',
    avatorImgPath: '',
    accessRole: '',
    access_token: getToken()
  },
  mutations: {
    setUserId (state, id) {
      state.userId = id
    },
    setEmail (state, email) {
      state.email = email
    },
    setAvator (state, avatorPath) {
      state.avatorImgPath = avatorPath
    },
    setAccessRole (state, role) {
      state.accessRole = role
    },
    setAccessToken (state, data) {
      let token = ''
      if (data.token) {
        state.access_token_type = data.token.token_type
        token = data.token.token_type + ' ' + data.token.access_token
      }
      state.access_token = token
      setToken(token)
    }
  },
  actions: {
    // 登录
    handleLogin ({ commit }, {email, password}) {
      email = email.trim()
      return new Promise((resolve, reject) => {
        login({
          email,
          password
        }).then(res => {
          const data = res.data
          commit('setAccessToken', data)
          resolve(res)
        }).catch(err => {
          reject(err)
        })
      })
    },
    // 退出登录
    handleLogOut ({ state, commit }) {
      return new Promise((resolve, reject) => {
        logout(state.token).then(() => {
          commit('setAccessToken', '')
          resolve()
        }).catch(err => {
          reject(err)
        })
        // 如果你的退出登录无需请求接口，则可以直接使用下面三行代码而无需使用logout调用接口
        // commit('setToken', '')
        // commit('setAccess', [])
        // resolve()
      })
    },
    // 获取用户相关信息
    getUserInfo ({ state, commit }) {
      return new Promise((resolve, reject) => {
        getUserInfo(state.access_token).then(res => {
          const data = res.data
          commit('setEmail', data.email)
          commit('setAvator', data.head_image.url)
          commit('setUserId', data.user_id)
          commit('setAccessRole', data.roles)
          resolve(data)
        }).catch(err => {
          reject(err)
        })
      })
    }
  }
}
