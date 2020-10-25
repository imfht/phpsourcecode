import axios from 'axios';

export default function (injection, Vue) {
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.interceptors.request.use(configuration => configuration, error => {
        injection.console.log(error);
        return Promise.reject(error);
    });
    Object.defineProperties(injection, {
        http: {
            get() {
                return axios;
            },
        },
    });
    Object.defineProperties(Vue, {
        http: {
            get() {
                return axios;
            },
        },
    });
    Object.defineProperties(Vue.prototype, {
        $http: {
            get() {
                return axios;
            },
        },
    });
}