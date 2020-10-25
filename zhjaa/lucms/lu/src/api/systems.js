import axios from '@/libs/api.request'

// =============== resources/systems/config-item-list.vue =========================
export const getTableData = (searchData) => {
  return axios.request({
    url: '/api/admin/system_configs',
    params: {
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const getGroup = () => {
  return axios.request({url: '/api/admin/system_configs/get_group', method: 'get'})
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/system_configs/' + id,
    method: 'delete'
  })
}

export const add = (formData) => {
  return axios.request({url: '/api/admin/system_configs', data: formData, method: 'post'})
}

export const edit = (id, formData) => {
  return axios.request({
    url: '/api/admin/system_configs/' + id,
    data: formData,
    method: 'patch'
  })
}

export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/system_configs/' + id,
    method: 'get'
  })
}
