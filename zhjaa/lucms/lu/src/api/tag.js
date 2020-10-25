import axios from '@/libs/api.request'

// =============== news-system/tags/list.vue =========================
export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/tags',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const getTagList = () => {
  return axios.request({url: '/api/admin/tags', method: 'get'})
}

export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/tags/' + id,
    method: 'get'
  })
}

export const addEdit = (saveData) => {
  return axios.request({url: '/api/admin/tags', data: saveData, method: 'post'})
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/tags/' + id,
    method: 'delete'
  })
}
