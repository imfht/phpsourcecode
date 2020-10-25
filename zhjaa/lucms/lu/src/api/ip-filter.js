import axios from '@/libs/api.request'

// =============== privileges/permissions/list.vue =========================
export const getTableData = (searchData) => {
  return axios.request({
    url: '/api/admin/ip_filters',
    params: {
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const addEdit = (saveData) => {
  return axios.request({url: '/api/admin/ip_filters', data: saveData, method: 'post'})
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/ip_filters/' + id,
    method: 'delete'
  })
}

export const getInfoById = (id) => {
  return axios.request({
    url: '/api/admin/ip_filters/' + id,
    method: 'get'
  })
}
