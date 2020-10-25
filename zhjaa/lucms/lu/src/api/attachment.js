
import axios from '@/libs/api.request'

// =============== resources/attachments/list.vue =========================
export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/attachments',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}


export const deleteAttachment = (attachment) => {
  return axios.request({
    url: '/api/admin/attachments/' + attachment,
    method: 'delete'
  })
}
