import axios from '@/libs/api.request'

// =============== privileges/permissions/list.vue =========================
export const getTableData = (searchData) => {
  return axios.request({
    url: '/api/admin/permissions',
    params: {
      search_data: JSON.stringify(searchData)
    },
    method: 'get'
  })
}

export const addEdit = (saveData) => {
  return axios.request({
    url: '/api/admin/permissions',
    data: saveData,
    method: 'post'
  })
}


export const destroy = (id) => {
  return axios.request({
    url: '/api/admin/permissions/' + id,
    method: 'delete'
  })
}

// =============== privileges/permissions/components/edit-permission.vue =========================

export const getInfoById = (id) => {
  return axios.request({
    url: '/api/admin/permissions/' + id,
    method: 'get'
  })
}
