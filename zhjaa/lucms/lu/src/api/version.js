import axios from '@/libs/api.request'

// =============== login/login.vue =========================
export const getVersionList = () => {
  return axios.request({url: 'api/admin/versions', method: 'get'})
}

export const newVersion = (saveData) => {
  return axios.request({url: 'api/admin/versions', data: saveData, method: 'post'})
}
