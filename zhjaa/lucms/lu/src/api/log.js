
import axios from '@/libs/api.request'

// =============== resources/logs/list.vue =========================
export const getTableData = (to_page, per_page, searchData) => {
  return axios.request({
    url: '/api/admin/logs',
    params: {
      page: to_page,
      per_page: per_page,
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}
