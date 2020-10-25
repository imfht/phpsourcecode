import axios from '@/libs/api.request'

export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/app_versions',
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
    url: '/api/admin/app_versions/' + id,
    method: 'delete'
  })
}

export const add = (formData) => {
  return axios.request({url: '/api/admin/app_versions', data: formData, method: 'post'})
}

export const edit = (id, formData) => {
  return axios.request({
    url: '/api/admin/app_versions/' + id,
    data: formData,
    method: 'patch'
  })
}

export const getInfoById = (id) => {
  return axios.request({
    url: 'api/admin/app_versions/' + id,
    method: 'get'
  })
}
