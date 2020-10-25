import axios from '@/libs/api.request'

export const getStatisticsData = () => {
    return axios.request({url: '/api/admin/statistics', method: 'get'})
}
