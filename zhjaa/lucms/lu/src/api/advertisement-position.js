import axios from '@/libs/api.request'

// =============== news-system/advertisement-positions/list.vue =========================
export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/advertisement_positions',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/advertisement_positions/' + id,
    method: 'delete'
  })
}

export const addEdit = (saveData) => {
  return axios.request({url: '/api/admin/advertisement_positions', data: saveData, method: 'post'})
}

export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/advertisement_positions/' + id,
    method: 'get'
  })
}
