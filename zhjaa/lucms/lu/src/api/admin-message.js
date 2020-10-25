import axios from '@/libs/api.request'

// =============== news-system/articles/list.vue =========================
export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/admin_messages',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const deleteAdminMessage = (admin_message) => {
  return axios.request({
    url: '/api/admin/admin_messages/' + admin_message,
    method: 'delete'
  })
}

export const deleteManyAdminMessage = (admin_message_ids) => {
  return axios.request({
    url: '/api/admin/admin_messages/' + admin_message_ids + '/many',
    method: 'delete'
  })
}

export const readMessages = (is_read_all, messageIds) => {
  return axios.request({
    url: '/api/admin/admin_messages/read_messages',
    data: {
      is_read_all: is_read_all,
      message_ids: messageIds
    },
    method: 'post'
  })
}
