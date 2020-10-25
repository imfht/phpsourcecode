import axios from '@/libs/api.request'

// =============== news-system/categories/list.vue =========================
export const getTableData = (searchData) => {
  return axios.request({
    url: '/api/admin/categories',
    params: {
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/categories/' + id,
    method: 'delete'
  })
}

export const addEdit = (saveData) => {
  return axios.request({url: '/api/admin/categories', data: saveData, method: 'post'})
}

export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/categories/' + id,
    method: 'get'
  })
}
