import axios from '@/libs/api.request'

// =============== login/login.vue =========================
export const login = ({email, password}) => {
  const data = {
    email,
    password
  }
  return axios.request({url: 'api/login', data, method: 'post'})
}

export const getUserInfo = (token) => {
  return axios.request({url: 'api/admin/users/current_user', method: 'get'})
}

export const logout = (token) => {
  return axios.request({url: 'api/logout', method: 'post'})
}

// =============== privileges/users/list.vue =========================

export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/users',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const getAllRole = () => {
  return axios.request({url: '/api/admin/all_roles', method: 'get'})
}

export const getUserRoles = (id) => {
  return axios.request({
    url: '/api/admin/users/' + id + '/roles',
    method: 'get'
  })
}

export const giveUserRole = (userId, roles) => {
  return axios.request({
    url: '/api/admin/give/' + userId + '/roles',
    data: {
      role: roles
    },
    method: 'post'
  })
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/users/' + id,
    method: 'delete'
  })
}

// =============== privileges/users/components/add-user.vue =========================

export const add = (formData) => {
  return axios.request({url: '/api/admin/users/', data: formData, method: 'post'})
}

export const edit = (id, formData) => {
  return axios.request({
    url: '/api/admin/users/' + id,
    data: formData,
    method: 'patch'
  })
}

export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/users/' + id,
    method: 'get'
  })
}
