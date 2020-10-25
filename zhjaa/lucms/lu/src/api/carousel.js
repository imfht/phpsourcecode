import axios from '@/libs/api.request'

// =============== news-system/categories/list.vue =========================
export const getTableData = () => {
  return axios.request({url: '/api/admin/news/carousels', method: 'get'})
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/news/carousels/' + id,
    method: 'delete'
  })
}

export const add = (saveData) => {
  return axios.request({url: '/api/admin/news/carousels', data: saveData, method: 'post'})
}

export const edit = (saveData, id) => {
  return axios.request({
    url: '/api/admin/news/carousels/' + id,
    data: saveData,
    method: 'patch'
  })
}


export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/news/carousels/' + id,
    method: 'get'
  })
}
