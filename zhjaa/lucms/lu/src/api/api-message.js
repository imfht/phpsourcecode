import axios from '@/libs/api.request'

// =============== news-system/articles/list.vue =========================
export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/api_messages',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const publishApiMessage = (formData) => {
  return axios.request({url: '/api/admin/api_messages', data: formData, method: 'post'})
}

export const getUserList = (phone) => {
  return axios.request({
    url: '/api/admin/api_messages/user_search/' + phone,
    method: 'get'
  })
}
