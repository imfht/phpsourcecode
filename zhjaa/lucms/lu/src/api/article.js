import axios from '@/libs/api.request'

// =============== news-system/articles/list.vue =========================
export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/articles',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const getArticleCategories = () => {
  return axios.request({url: '/api/admin/categories/all', method: 'get'})
}

export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/articles/' + id,
    method: 'delete'
  })
}

export const add = (formData) => {
  return axios.request({url: '/api/admin/articles', data: formData, method: 'post'})
}

export const edit = (id, formData) => {
  return axios.request({
    url: '/api/admin/articles/' + id,
    data: formData,
    method: 'patch'
  })
}

export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/articles/' + id,
    method: 'get'
  })
}
